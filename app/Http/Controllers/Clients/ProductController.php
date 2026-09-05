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

class ProductController extends Controller
{
    public function show(Request $request, $slug)
    {
        $product = Product::published()->with([
            'category:id,name,slug',
            'brand:id,name,slug',
            'series:id,name,slug',
            'thumbnailMedia',
            'galleryMedia',
            'catalogMedia',
            'reviews' => fn($q) => $q->where('status', 'approved')->orWhereNull('status')->orderByDesc('created_at')->take(20),
            'reviews.user:id,name',
        ])->where('slug', $slug)->firstOrFail();

        // Tăng lượt xem (chỉ đếm 1 lần trong phiên session để tối ưu tốc độ DB và chống F5 ảo)
        $viewKey = 'viewed_prod_' . $product->id;
        if (!session()->has($viewKey)) {
            \Illuminate\Support\Facades\DB::table('products')->where('id', $product->id)->increment('views_count');
            session()->put($viewKey, true);
        }

        // Lưu lịch sử xem sản phẩm cho người dùng
        $viewedList = session()->get('viewed_products', []);
        if (($k = array_search($product->id, $viewedList)) !== false) {
            unset($viewedList[$k]);
        }
        array_unshift($viewedList, $product->id);
        session()->put('viewed_products', array_slice($viewedList, 0, 10));

        // Danh sách tối đa 15 model mới nhất trong cùng Series (Cached)
        $seriesProducts = collect();
        $embeddedSeriesModels = [];

        if ($product->series_id) {
            $seriesData = \Illuminate\Support\Facades\Cache::remember("series_models_embedded_{$product->series_id}", 3600, function () use ($product) {
                $seriesModels = Product::published()
                    ->where('series_id', $product->series_id)
                    ->with(['thumbnailMedia', 'galleryMedia', 'catalogMedia'])
                    ->orderBy('sku', 'asc')
                    ->orderBy('name', 'asc')
                    ->get([
                        'id', 'name', 'slug', 'sku', 'price', 'sale_price',
                        'short_description', 'content', 'status', 'meta_title',
                        'meta_description', 'series_id'
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
                        'short_description' => $m->short_description,
                        'content' => $m->content,
                        'status' => $m->status,
                        'is_active' => $m->status === 'active',
                        'thumbnail_url' => $m->thumbnailMedia->first()?->url ?? asset('storage/clients/imgs/products/no-image.png'),
                        'gallery' => $m->galleryMedia->map(fn($g) => [
                            'id' => $g->id,
                            'url' => $g->url,
                            'alt' => $g->alt ?? $m->name
                        ])->values()->all(),
                        'catalogs' => $m->catalogMedia->map(fn($c) => [
                            'id' => $c->id,
                            'url' => $c->url,
                            'download_url' => route('documents.download', $c->id),
                            'filename' => $c->original_name ?: ($c->filename ?: 'Tài liệu sản phẩm')
                        ])->values()->all(),
                        'meta_title' => ($m->meta_title ?? $m->name) . ' | MISUTECH',
                        'meta_description' => $m->meta_description ?? '',
                        'url' => route('product.show', $m->slug),
                    ];
                    $embedded[$m->slug] = $itemData;
                    $list[] = $itemData;
                }

                return [
                    'embedded' => $embedded,
                    'list' => $list
                ];
            });

            $embeddedSeriesModels = $seriesData['embedded'] ?? [];
            $seriesProducts = collect($seriesData['list'] ?? [])->map(fn($i) => (object)$i);
        }

        // Sản phẩm liên quan: Lấy 10 sản phẩm cùng danh mục trong 1 query duy nhất
        $relatedProducts = collect();
        if ($product->category_id) {
            $relatedProductIds = \Illuminate\Support\Facades\Cache::remember("category_related_product_ids_{$product->category_id}", 1800, function () use ($product) {
                return Product::published()
                    ->where('category_id', $product->category_id)
                    ->latest('id')
                    ->take(15)
                    ->pluck('id')
                    ->toArray();
            });

            if (!empty($relatedProductIds)) {
                $filteredIds = array_values(array_diff($relatedProductIds, [$product->id]));
                $targetIds = array_slice($filteredIds, 0, 10);

                if (!empty($targetIds)) {
                    $relatedProductsKeyed = Product::published()
                        ->select(['id', 'name', 'slug', 'price', 'sale_price', 'rating_average', 'reviews_count', 'category_id'])
                        ->with('thumbnailMedia')
                        ->whereIn('id', $targetIds)
                        ->get()
                        ->keyBy('id');

                    foreach ($targetIds as $tId) {
                        if (isset($relatedProductsKeyed[$tId])) {
                            $relatedProducts->push($relatedProductsKeyed[$tId]);
                        }
                    }
                }
            }
        }

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
}
