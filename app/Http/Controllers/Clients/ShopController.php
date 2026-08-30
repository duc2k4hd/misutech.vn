<?php

namespace App\Http\Controllers\Clients;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Cache;

class ShopController extends Controller
{
    /**
     * Danh sách cột sản phẩm cần thiết cho hiển thị thẻ sản phẩm (tránh load cột content nặng vào RAM)
     */
    const PRODUCT_CARD_COLUMNS = [
        'id', 'name', 'slug', 'sku', 'price', 'sale_price',
        'rating_average', 'reviews_count', 'category_id', 'brand_id',
        'short_description', 'published_at', 'status'
    ];

    /**
     * Lấy danh sách danh mục cha bên Sidebar kèm số lượng sản phẩm (In-memory & Cached)
     */
    public static function getSidebarCategories()
    {
        $data = Cache::remember('shop_sidebar_categories', 3600, function () {
            $allCats = Category::where('type', 'product')
                ->where('status', 'active')
                ->select(['id', 'name', 'slug', 'parent_id', 'icon', 'position'])
                ->orderBy('position', 'asc')
                ->get();

            if ($allCats->isEmpty()) {
                return [];
            }

            // Xây dựng cây con cháu in-memory
            $childrenMap = [];
            foreach ($allCats as $c) {
                $pId = $c->parent_id ?: 0;
                $childrenMap[$pId][] = $c->id;
            }

            $getDescendantIds = function ($parentId) use (&$getDescendantIds, &$childrenMap) {
                $ids = [$parentId];
                if (isset($childrenMap[$parentId])) {
                    foreach ($childrenMap[$parentId] as $childId) {
                        $ids = array_merge($ids, $getDescendantIds($childId));
                    }
                }
                return $ids;
            };

            // Đếm sản phẩm từng danh mục trong 1 query GROUP BY duy nhất
            $productCounts = Product::published()
                ->selectRaw('category_id, COUNT(*) as aggregate')
                ->groupBy('category_id')
                ->pluck('aggregate', 'category_id')
                ->toArray();

            $parents = $allCats->whereNull('parent_id');
            $result = [];
            foreach ($parents as $parent) {
                $allIds = $getDescendantIds($parent->id);
                $pCount = 0;
                foreach ($allIds as $cid) {
                    $pCount += $productCounts[$cid] ?? 0;
                }
                $result[] = [
                    'id' => $parent->id,
                    'name' => $parent->name,
                    'slug' => $parent->slug,
                    'icon' => $parent->icon,
                    'products_count' => $pCount,
                ];
            }
            return $result;
        });

        return collect($data)->map(function ($item) {
            return (object)$item;
        });
    }

    /**
     * Lấy danh sách thương hiệu kèm số lượng sản phẩm (Cached)
     */
    public static function getFilterBrands()
    {
        $data = Cache::remember('shop_filter_brands', 3600, function () {
            return Brand::select(['id', 'name', 'slug', 'logo'])
                ->withCount(['products' => function ($q) {
                    $q->published();
                }])
                ->orderBy('name', 'asc')
                ->get()
                ->toArray();
        });

        return collect($data)->map(function ($item) {
            return (object)$item;
        });
    }

    /**
     * Lấy mức giá cao nhất của sản phẩm (Cached)
     */
    public static function getMaxPrice()
    {
        return Cache::remember('shop_max_price', 3600, function () {
            return Product::published()->max('price') ?? 0;
        });
    }

    private function buildProductQuery(Request $request)
    {
        $query = Product::published()
            ->select(self::PRODUCT_CARD_COLUMNS)
            ->with([
                'category:id,name,slug',
                'brand:id,name,slug',
                'thumbnailMedia'
            ]);

        // 1. Search term (tim-kiem)
        if ($request->filled('tim-kiem')) {
            $keyword = trim($request->input('tim-kiem'));
            $words = array_filter(explode(' ', $keyword));

            $query->where(function($q) use ($keyword, $words) {
                $q->where('name', 'like', '%' . $keyword . '%');
                
                if (count($words) > 1) {
                    $q->orWhere(function($subQ) use ($words) {
                        foreach ($words as $word) {
                            $subQ->orWhere('name', 'like', '%' . $word . '%');
                        }
                    });
                }
            });

            $query->orderByRaw("
                CASE 
                    WHEN name = ? THEN 1
                    WHEN name LIKE ? THEN 2
                    WHEN name LIKE ? THEN 3
                    ELSE 4
                END ASC
            ", [$keyword, $keyword . '%', '%' . $keyword . '%']);
        }

        // 2. Category (Lấy toàn bộ category con cháu qua In-memory mapping)
        if ($request->filled('category')) {
            $catSlug = $request->input('category');
            $descMap = Category::getDescendantsMap('product');
            $catId = $descMap['slug_to_id'][$catSlug] ?? null;

            if ($catId && isset($descMap['descendants'][$catId])) {
                $query->whereIn('category_id', $descMap['descendants'][$catId]);
            } else {
                $query->where('id', 0);
            }
        }

        // 3. Brands
        if ($request->filled('brands')) {
            $brands = is_array($request->input('brands')) ? $request->input('brands') : explode(',', $request->input('brands'));
            $query->whereHas('brand', function ($q) use ($brands) {
                $q->whereIn('slug', $brands);
            });
        }

        // 4. Price range
        if ($request->filled('min_price')) {
            $query->whereRaw('COALESCE(sale_price, price) >= ?', [$request->input('min_price')]);
        }
        if ($request->filled('max_price')) {
            $query->whereRaw('COALESCE(sale_price, price) <= ?', [$request->input('max_price')]);
        }

        // 5. Sorting
        $sort = $request->input('sort', 'featured');
        if (!$request->filled('tim-kiem') || $sort !== 'featured') {
            switch ($sort) {
                case 'price-asc':
                    $query->orderByRaw('COALESCE(sale_price, price) asc');
                    break;
                case 'price-desc':
                    $query->orderByRaw('COALESCE(sale_price, price) desc');
                    break;
                case 'name':
                    $query->orderBy('name', 'asc');
                    break;
                case 'featured':
                default:
                    $query->orderBy('id', 'desc');
                    break;
            }
        }

        return $query;
    }

    public function index(Request $request)
    {
        $parentCategories = self::getSidebarCategories();
        $brands = self::getFilterBrands();
        $maxPrice = self::getMaxPrice();

        $query = $this->buildProductQuery($request);
        $totalProducts = (clone $query)->count();
        $products = $query->take(21)->get();

        $viewedProductIds = session()->get('viewed_products', []);
        $viewedProducts = [];
        if (!empty($viewedProductIds)) {
            $placeholders = implode(',', array_fill(0, count($viewedProductIds), '?'));
            $viewedProducts = Product::published()
                ->select(self::PRODUCT_CARD_COLUMNS)
                ->with('thumbnailMedia')
                ->whereIn('id', $viewedProductIds)
                ->orderByRaw("FIELD(id, $placeholders)", $viewedProductIds)
                ->take(8)
                ->get();
        }

        return view('clients.pages.shop.index', compact('parentCategories', 'brands', 'products', 'totalProducts', 'maxPrice', 'viewedProducts'));
    }

    public function loadMore(Request $request)
    {
        $offset = $request->input('offset', 0);
        $limit = $offset == 0 ? 21 : 6;

        $query = $this->buildProductQuery($request);
        $totalProducts = (clone $query)->count();
        $products = $query->skip($offset)->take($limit)->get();

        $template = <<<'BLADE'
        <article class="misutech_home_product_card" data-name="{{ $product->name }}"
            data-price="{{ $product->price }}"
            data-brand="{{ $product->brand ? $product->brand->slug : '' }}"
            data-availability="in-stock"
            data-category="{{ $product->category ? $product->category->slug : '' }}">

            @if ($product->sale_price && $product->sale_price < $product->price)
                <span
                    class="misutech_home_sale_badge">-{{ round((($product->price - $product->sale_price) / $product->price) * 100) }}%</span>
            @endif

            <div class="misutech_home_product_media">
                <a href="{{ route('product.show', $product->slug) }}">
                    <img src="{{ $product->thumbnailMedia->first()?->url ?? asset('storage/clients/imgs/products/no-image.png') }}"
                        alt="{{ $product->name ?? 'No image' }}"
                        loading="lazy"
                        decoding="async">
                </a>
                <div class="misutech_home_product_actions">
                    <button class="misutech_home_product_action" type="button" data-favorite
                        aria-label="Thêm vào yêu thích">♡</button>
                    <button class="misutech_home_product_action" type="button" data-cart
                        aria-label="Thêm vào giỏ hàng">＋</button>
                </div>
            </div>

            <div class="misutech_home_product_info">
                @if ($product->brand || $product->sku)
                    <div class="misutech_home_product_meta_tags">
                        @if ($product->brand)
                            <span class="product_meta_brand">{{ $product->brand->name }}</span>
                        @endif
                        @if ($product->sku)
                            <span class="product_meta_sku">Mã: {{ $product->sku }}</span>
                        @endif
                    </div>
                @endif

                <h3 class="misutech_home_product_name">
                    <a href="{{ route('product.show', $product->slug) }}"
                        style="color: inherit; text-decoration: none;">{{ $product->name }}</a>
                </h3>

                <div class="misutech_home_product_rating">
                    @for ($i = 1; $i <= 5; $i++)
                        @if ($i <= round($product->rating_average ?? 5))
                            <span class="star-filled">★</span>
                        @else
                            <span class="star-empty">☆</span>
                        @endif
                    @endfor
                    <span class="rating_text">({{ number_format($product->rating_average ?? 5, 1) }})</span>
                </div>

                {{-- Khung thông tin chỉ hiển thị ở Dạng Danh Sách (1 Cột) --}}
                <div class="misutech_home_product_list_desc">
                    @if (!empty($product->short_description))
                        <p class="product_short_desc">{{ Str::limit(strip_tags($product->short_description), 140) }}</p>
                    @endif
                    <ul class="product_feature_badges">
                        <li><span class="badge_icon">✓</span> Hàng chính hãng 100%</li>
                        <li><span class="badge_icon">✓</span> Bảo hành 12 tháng</li>
                        <li><span class="badge_icon">✓</span> Đầy đủ CO/CQ & VAT</li>
                        <li><span class="badge_icon">✓</span> Sẵn hàng giao nhanh</li>
                    </ul>
                </div>
            </div>

            <div class="misutech_home_product_footer_action">
                <div class="misutech_home_product_stock_badge">
                    <span class="stock_dot"></span> Còn hàng
                </div>

                <div class="misutech_home_product_price_wrapper">
                    @if ($product->sale_price && $product->sale_price < $product->price)
                        <div class="misutech_home_product_price_line">
                            <strong
                                class="misutech_home_product_price">{{ number_format($product->sale_price, 0, ',', '.') }}
                                VNĐ</strong>
                            <del class="misutech_home_product_old_price">{{ number_format($product->price, 0, ',', '.') }}
                                VNĐ</del>
                        </div>
                    @elseif($product->price > 0)
                        <div class="misutech_home_product_price_line">
                            <strong
                                class="misutech_home_product_price">{{ number_format($product->price, 0, ',', '.') }}
                                VNĐ</strong>
                        </div>
                    @else
                        <div class="misutech_home_product_price_line">
                            <strong class="misutech_home_product_price" style="color: #003b70;">Liên hệ báo giá</strong>
                        </div>
                    @endif
                </div>

                <div class="misutech_home_product_list_buttons">
                    <a href="{{ route('product.show', $product->slug) }}" class="btn_view_product">
                        Xem chi tiết
                    </a>
                    <button class="btn_add_to_cart" type="button" data-cart>
                        + Thêm giỏ hàng
                    </button>
                </div>
            </div>
        </article>
        BLADE;

        $html = '';
        foreach ($products as $product) {
            $html .= Blade::render($template, compact('product'));
        }

        return response()->json([
            'html' => $html,
            'count' => $products->count(),
            'total' => $totalProducts,
        ]);
    }

    public function brands()
    {
        $brandsData = \Illuminate\Support\Facades\Cache::remember('all_brands_page', 3600, function () {
            return Brand::select(['id', 'name', 'slug', 'logo'])
                ->withCount('products')
                ->orderBy('name')
                ->get()
                ->toArray();
        });
        $brands = collect($brandsData)->map(fn($item) => (object)$item);

        return view('clients.pages.brands.index', compact('brands'));
    }

    public function cart()
    {
        $cartItems = session('cart_items', []);
        return view('clients.pages.cart.index', compact('cartItems'));
    }

    public function addCart(Request $request)
    {
        $key = 'add-cart:' . $request->ip();
        
        if (\Illuminate\Support\Facades\RateLimiter::tooManyAttempts($key, 1)) {
            return response()->json([
                'success' => false,
                'message' => 'Vui lòng chậm lại tránh spam!',
            ], 429);
        }
        
        \Illuminate\Support\Facades\RateLimiter::hit($key, 1);

        $productName = $request->input('product_name');
        $quantity = $request->input('quantity', 1);
        $product = \App\Models\Product::with('thumbnailMedia')->where('name', $productName)->first();

        $cartItems = session('cart_items', []);

        if ($product) {
            if (isset($cartItems[$product->id])) {
                $cartItems[$product->id]['quantity'] += $quantity;
            } else {
                $cartItems[$product->id] = [
                    'id' => $product->id,
                    'name' => $product->name,
                    'slug' => $product->slug,
                    'price' => $product->sale_price ?: $product->price,
                    'thumbnail_url' => $product->thumbnailMedia->first()?->url,
                    'quantity' => $quantity,
                ];
            }
            
            // Limit max quantity to 1000
            if ($cartItems[$product->id]['quantity'] > 1000) {
                $cartItems[$product->id]['quantity'] = 1000;
            }
            
            session(['cart_items' => $cartItems]);
        }
        
        // Always calculate cart count based on actual items in session
        $cartCount = array_sum(array_column($cartItems, 'quantity'));
        session(['cart_count' => $cartCount]);
        
        return response()->json([
            'success' => true,
            'message' => 'Đã thêm vào giỏ hàng',
            'cart_count' => $cartCount
        ]);
    }
    
    public function updateCart(Request $request)
    {
        $productId = $request->input('product_id');
        $quantity = (int) $request->input('quantity', 1);
        
        if ($quantity < 0) $quantity = 0;
        if ($quantity > 1000) $quantity = 1000;
        
        $cartItems = session('cart_items', []);
        
        if (isset($cartItems[$productId])) {
            if ($quantity == 0) {
                unset($cartItems[$productId]);
            } else {
                $cartItems[$productId]['quantity'] = $quantity;
            }
            session(['cart_items' => $cartItems]);
        }
        
        $cartCount = array_sum(array_column($cartItems, 'quantity'));
        session(['cart_count' => $cartCount]);
        
        $totalPrice = 0;
        foreach ($cartItems as $item) {
            $totalPrice += $item['price'] * $item['quantity'];
        }
        
        return response()->json([
            'success' => true,
            'cart_count' => $cartCount,
            'total_price' => $totalPrice,
            'item_total' => isset($cartItems[$productId]) ? $cartItems[$productId]['price'] * $cartItems[$productId]['quantity'] : 0,
            'cart_items' => array_values($cartItems)
        ]);
    }
    
    public function clearCart()
    {
        session()->forget(['cart_items', 'cart_count']);
        return response()->json([
            'success' => true,
            'message' => 'Đã xóa toàn bộ giỏ hàng'
        ]);
    }
}
