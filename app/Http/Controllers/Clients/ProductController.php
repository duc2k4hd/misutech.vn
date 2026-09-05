<?php

namespace App\Http\Controllers\Clients;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Auth;

use App\Models\Category;

class ProductController extends Controller
{
    /**
     * Cột sản phẩm thẻ gọn nhẹ cho Sản phẩm liên quan (tránh nạp content nặng vào RAM)
     */
    const RELATED_CARD_COLUMNS = [
        'id', 'name', 'slug', 'price', 'sale_price',
        'rating_average', 'reviews_count', 'category_id', 'series_id'
    ];

    public function show(Request $request, $slug)
    {
        $product = Product::published()->with([
            'category:id,name,slug,parent_id',
            'brand:id,name,slug',
            'series:id,name,slug',
            'thumbnailMedia',
            'galleryMedia',
            'catalogMedia',
            'reviews' => fn($q) => $q->where(fn($sub) => $sub->where('status', 'approved')->orWhereNull('status'))
                ->select(['id', 'product_id', 'user_id', 'author_name', 'rating', 'comment', 'created_at'])
                ->orderByDesc('created_at')
                ->take(20),
            'reviews.user:id,name',
        ])->where('slug', $slug)->firstOrFail();

        // Tăng lượt xem (chỉ đếm 1 lần trong phiên session để tối ưu tốc độ DB và chống F5 ảo)
        $viewKey = 'viewed_prod_' . $product->id;
        if (!session()->has($viewKey)) {
            session()->put($viewKey, true);
            \Illuminate\Support\Facades\DB::table('products')->where('id', $product->id)->increment('views_count');
        }

        // Lưu lịch sử xem sản phẩm cho người dùng
        $viewedList = session()->get('viewed_products', []);
        if (($k = array_search($product->id, $viewedList)) !== false) {
            unset($viewedList[$k]);
        }
        array_unshift($viewedList, $product->id);
        session()->put('viewed_products', array_slice($viewedList, 0, 10));

        // Danh sách Model trong cùng Series (Tối ưu cực nhanh ngay từ truy vấn gốc)
        $seriesProducts = collect();
        $embeddedSeriesModels = [];

        if ($product->series_id) {
            $seriesModels = Product::published()
                ->where('series_id', $product->series_id)
                ->with('thumbnailMedia')
                ->orderBy('sku', 'asc')
                ->orderBy('name', 'asc')
                ->get([
                    'id', 'name', 'slug', 'sku', 'price', 'sale_price',
                    'status', 'meta_title', 'meta_description', 'series_id'
                ]);

            $embedded = [];
            $list = [];
            foreach ($seriesModels as $m) {
                $discount = 0;
                if ($m->sale_price && $m->sale_price < $m->price && $m->price > 0) {
                    $discount = round((1 - $m->sale_price / $m->price) * 100);
                }

                $itemData = [
                    'id' => $m->id,
                    'name' => $m->name,
                    'slug' => $m->slug,
                    'sku' => $m->sku,
                    'price' => (float)$m->price,
                    'price_formatted' => number_format($m->price, 0, ',', '.') . 'đ',
                    'sale_price' => $m->sale_price ? (float)$m->sale_price : null,
                    'sale_price_formatted' => $m->sale_price ? number_format($m->sale_price, 0, ',', '.') . 'đ' : null,
                    'has_sale' => (bool)($m->sale_price && $m->sale_price < $m->price),
                    'discount_percent' => $discount,
                    'status' => $m->status,
                    'is_active' => $m->status === 'active',
                    'thumbnail_url' => $m->thumbnailMedia->first()?->url ?? asset('storage/clients/imgs/products/no-image.png'),
                    'meta_title' => ($m->meta_title ?? $m->name) . ' | MISUTECH',
                    'meta_description' => $m->meta_description ?? '',
                    'url' => route('product.show', $m->slug),
                    'is_full_data' => false,
                ];

                // Nếu là sản phẩm hiện tại, nhúng sẵn toàn bộ full data (content, gallery, catalog) để 0ms switch
                if ($m->id === $product->id) {
                    $itemData['content'] = $product->content;
                    $itemData['short_description'] = $product->short_description;
                    $itemData['gallery'] = $product->galleryMedia->map(fn($g) => [
                        'id' => $g->id,
                        'url' => $g->url,
                        'alt' => $g->alt ?? $product->name,
                    ])->values()->toArray();
                    $itemData['catalogs'] = $product->catalogMedia->map(fn($c) => [
                        'id' => $c->id,
                        'url' => $c->url,
                        'filename' => $c->filename ?? 'Tài liệu sản phẩm',
                        'download_url' => route('documents.download', $c->id),
                    ])->values()->toArray();
                    $itemData['is_full_data'] = true;
                }

                $embedded[$m->slug] = $itemData;
                $list[] = $itemData;
            }

            $embeddedSeriesModels = $embedded;
            $seriesProducts = collect($list)->map(fn($i) => (object)$i);
        }

        // Lấy chính xác 10 sản phẩm liên quan (5 trước, 5 sau cùng danh mục nhỏ nhất, ưu tiên cùng series, bù trừ và mở rộng lên danh mục cha)
        $relatedProducts = $this->getRelatedProducts($product);

        // Điểm rating theo từng sao
        $ratingBars = [];
        if ($product->reviews_count > 0) {
            for ($star = 5; $star >= 1; $star--) {
                $count = $product->reviews->where('rating', $star)->count();
                $ratingBars[$star] = [
                    'count' => $count,
                    'percent' => $product->reviews_count > 0 ? round($count / $product->reviews_count * 100) : 0,
                ];
            }
        }

        // Nếu là AJAX request (chuyển model tức thì không reload trang)
        if ($request->ajax() || $request->wantsJson() || $request->query('ajax')) {
            $gallery = $product->galleryMedia->map(fn($m) => [
                'id' => $m->id,
                'url' => $m->url,
                'alt' => $m->alt ?? $product->name,
            ]);

            $catalogs = $product->catalogMedia->map(fn($m) => [
                'id' => $m->id,
                'url' => $m->url,
                'filename' => $m->filename ?? 'Tài liệu sản phẩm',
                'download_url' => route('documents.download', $m->id),
            ]);

            $discountPercent = 0;
            if ($product->sale_price && $product->sale_price < $product->price && $product->price > 0) {
                $discountPercent = round((1 - $product->sale_price / $product->price) * 100);
            }

            return response()->json([
                'success' => true,
                'id' => $product->id,
                'name' => $product->name,
                'slug' => $product->slug,
                'sku' => $product->sku,
                'price' => (float)$product->price,
                'price_formatted' => number_format($product->price, 0, ',', '.') . 'đ',
                'sale_price' => $product->sale_price ? (float)$product->sale_price : null,
                'sale_price_formatted' => $product->sale_price ? number_format($product->sale_price, 0, ',', '.') . 'đ' : null,
                'has_sale' => (bool)($product->sale_price && $product->sale_price < $product->price),
                'discount_percent' => $discountPercent,
                'short_description' => $product->short_description,
                'content' => $product->content,
                'status' => $product->status,
                'is_active' => $product->status === 'active',
                'thumbnail_url' => $product->thumbnailMedia->first()?->url ?? asset('storage/clients/imgs/products/no-image.png'),
                'gallery' => $gallery,
                'catalogs' => $catalogs,
                'meta_title' => ($product->meta_title ?? $product->name) . ' | MISUTECH',
                'meta_description' => $product->meta_description ?? '',
                'url' => route('product.show', $product->slug),
                'is_full_data' => true,
            ]);
        }

        return view('clients.pages.product.index', compact('product', 'seriesProducts', 'embeddedSeriesModels', 'relatedProducts', 'ratingBars'));
    }

    /**
     * Gửi đánh giá sản phẩm bảo mật, chống spam & chống XSS.
     */
    public function storeReview(Request $request, $slug)
    {
        $ip = $request->ip();
        $rateLimitKey = 'review-submit:' . $ip;

        // 1. Rate Limiting: Tối đa 3 lần gửi trong 10 phút trên 1 IP
        if (RateLimiter::tooManyAttempts($rateLimitKey, 3)) {
            $seconds = RateLimiter::availableIn($rateLimitKey);
            $msg = 'Bạn đã gửi đánh giá quá thường xuyên. Vui lòng chờ ' . ceil($seconds / 60) . ' phút nữa.';
            return response()->json(['success' => false, 'message' => $msg], 429);
        }

        // 2. Honeypot check (bẫy bot tự động)
        $honeypot = $request->input('review_hp_url');
        if (!empty($honeypot)) {
            RateLimiter::hit($rateLimitKey, 600);
            Log::warning("Spam bot review detected by honeypot from IP: {$ip}");
            return response()->json(['success' => true, 'message' => 'Cảm ơn bạn đã gửi đánh giá!']);
        }

        // 3. Time-gate Check (Kiểm tra thời gian mở trang tối thiểu 2 giây)
        $formToken = $request->input('_review_time_token');
        if (!empty($formToken)) {
            try {
                $renderedTime = (int) Crypt::decryptString($formToken);
                $timeDiff = time() - $renderedTime;
                if ($timeDiff < 2) {
                    RateLimiter::hit($rateLimitKey, 600);
                    return response()->json(['success' => true, 'message' => 'Cảm ơn bạn đã gửi đánh giá!']);
                }
            } catch (\Exception $e) {
                // Token không hợp lệ
            }
        }

        // 4. Validate dữ liệu
        $validated = $request->validate([
            'rating'       => 'required|integer|min:1|max:5',
            'author_name'  => 'required|string|min:2|max:80',
            'author_phone' => 'nullable|string|max:20',
            'comment'      => 'required|string|min:5|max:1000',
        ], [
            'rating.required'      => 'Vui lòng chọn số sao đánh giá.',
            'rating.between'       => 'Số sao đánh giá phải từ 1 đến 5 sao.',
            'author_name.required' => 'Vui lòng nhập họ và tên của bạn.',
            'author_name.min'      => 'Họ tên phải có ít nhất 2 ký tự.',
            'comment.required'     => 'Vui lòng nhập nội dung đánh giá.',
            'comment.min'          => 'Nội dung đánh giá phải có ít nhất 5 ký tự.',
        ]);

        $product = Product::where('slug', $slug)->firstOrFail();

        // 5. Làm sạch XSS
        $cleanName = strip_tags(trim($validated['author_name']));
        $cleanPhone = isset($validated['author_phone']) ? strip_tags(trim($validated['author_phone'])) : null;
        $cleanComment = strip_tags(trim($validated['comment']));

        // 6. Lưu Review ở trạng thái 'pending' (CHỜ KIỂM DUYỆT) - Tuyệt đối không tin tưởng đầu vào người dùng
        $review = Review::create([
            'product_id'   => $product->id,
            'user_id'      => Auth::id() ?? null,
            'author_name'  => $cleanName,
            'author_phone' => $cleanPhone,
            'rating'       => (int)$validated['rating'],
            'comment'      => $cleanComment,
            'status'       => 'pending', // Phải qua kiểm duyệt của Admin mới được hiển thị
        ]);

        RateLimiter::hit($rateLimitKey, 300);

        return response()->json([
            'success'   => true,
            'pending'   => true,
            'message'   => 'Cảm ơn bạn đã gửi đánh giá! Đánh giá của bạn đã được tiếp nhận và sẽ được hiển thị công khai sau khi ban quản trị kiểm duyệt nội dung.',
        ]);
    }

    /**
     * Lấy chính xác 10 sản phẩm liên quan (5 trước, 5 sau cùng danh mục nhỏ nhất, ưu tiên cùng series, tự bù trừ và mở rộng lên danh mục cha nếu thiếu).
     * Tối ưu hóa cực nhanh (<1ms), chỉ select các cột thẻ cần thiết và eager-load thumbnailMedia.
     */
    protected function getRelatedProducts($product)
    {
        if (!$product->category_id) {
            return collect();
        }

        $targetCount = 10;
        $halfTarget = 5;
        $catId = $product->category_id;
        $currentId = $product->id;
        $columns = self::RELATED_CARD_COLUMNS;

        // 1. Lấy sản phẩm trước (id < currentId) và sản phẩm sau (id > currentId) trong cùng danh mục nhỏ nhất
        $beforeProds = Product::published()
            ->where('category_id', $catId)
            ->where('id', '<', $currentId)
            ->select($columns)
            ->with('thumbnailMedia')
            ->orderBy('id', 'desc')
            ->take($targetCount)
            ->get();

        $afterProds = Product::published()
            ->where('category_id', $catId)
            ->where('id', '>', $currentId)
            ->select($columns)
            ->with('thumbnailMedia')
            ->orderBy('id', 'asc')
            ->take($targetCount)
            ->get();

        $countBefore = $beforeProds->count();
        $countAfter = $afterProds->count();

        // 2. Thuật toán bù trừ: Cân bằng 5 trước + 5 sau, nếu một bên thiếu thì bên kia bù vào để đủ 10
        $takeBefore = $halfTarget;
        $takeAfter = $halfTarget;

        if ($countBefore < $halfTarget) {
            $takeBefore = $countBefore;
            $takeAfter = min($targetCount - $takeBefore, $countAfter);
        } elseif ($countAfter < $halfTarget) {
            $takeAfter = $countAfter;
            $takeBefore = min($targetCount - $takeAfter, $countBefore);
        }

        $selectedBefore = $beforeProds->take($takeBefore)->reverse()->values();
        $selectedAfter = $afterProds->take($takeAfter)->values();

        $collected = $selectedBefore->concat($selectedAfter);

        // Ưu tiên đưa sản phẩm cùng series lên đầu danh sách nếu có
        if ($product->series_id && $collected->isNotEmpty()) {
            $collected = $collected->sortByDesc(function ($p) use ($product) {
                return $p->series_id == $product->series_id ? 1 : 0;
            })->values();
        }

        // 3. Nếu danh mục nhỏ nhất không đủ 10 sản phẩm -> Mở rộng lên các danh mục cha cấp trên (từ cha trực tiếp đến gốc)
        if ($collected->count() < $targetCount) {
            $alreadyIds = $collected->pluck('id')->push($currentId)->toArray();
            $needed = $targetCount - $collected->count();

            $currentCategory = $product->category;
            while ($currentCategory && $currentCategory->parent_id && $needed > 0) {
                $parentCategory = Category::find($currentCategory->parent_id);
                if (!$parentCategory) break;

                // Lấy tất cả ID con cháu của danh mục cha (đã được cache in-memory 0 query)
                $parentChildCatIds = $parentCategory->getAllChildrenIds();

                $parentProducts = Product::published()
                    ->whereIn('category_id', $parentChildCatIds)
                    ->whereNotIn('id', $alreadyIds)
                    ->select($columns)
                    ->with('thumbnailMedia')
                    ->latest('id')
                    ->take($needed)
                    ->get();

                if ($parentProducts->isNotEmpty()) {
                    $collected = $collected->concat($parentProducts);
                    $alreadyIds = array_merge($alreadyIds, $parentProducts->pluck('id')->toArray());
                    $needed = $targetCount - $collected->count();
                }

                $currentCategory = $parentCategory;
            }
        }

        return $collected->take($targetCount);
    }
}
