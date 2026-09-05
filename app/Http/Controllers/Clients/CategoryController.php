<?php

namespace App\Http\Controllers\Clients;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Product;
use App\Models\Brand;
use Illuminate\Support\Facades\Cache;

class CategoryController extends Controller
{
    private function buildProductQuery(Request $request)
    {
        $query = Product::published()
            ->select(ShopController::PRODUCT_CARD_COLUMNS)
            ->with([
                'category:id,name,slug',
                'brand:id,name,slug',
                'thumbnailMedia'
            ]);

        if ($request->filled('q')) {
            $query->where('name', 'like', '%' . $request->input('q') . '%');
        }

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

        if ($request->filled('brands')) {
            $brands = is_array($request->input('brands')) ? $request->input('brands') : array_filter(explode(',', (string)$request->input('brands')));
            if (!empty($brands)) {
                $query->whereHas('brand', function ($q) use ($brands) {
                    $q->whereIn('slug', $brands);
                });
            }
        }

        // 4. Price ranges (Preset brackets like 2000000-3000000,5000000-8000000)
        if ($request->filled('price_ranges')) {
            $ranges = is_array($request->input('price_ranges')) ? $request->input('price_ranges') : array_filter(explode(',', (string)$request->input('price_ranges')));
            if (!empty($ranges)) {
                $query->where(function($q) use ($ranges) {
                    foreach ($ranges as $range) {
                        $parts = explode('-', $range);
                        if (count($parts) === 2) {
                            $min = (float)$parts[0];
                            $max = (float)$parts[1];
                            $q->orWhere(function($subQ) use ($min, $max) {
                                $subQ->whereRaw('COALESCE(sale_price, price) >= ?', [$min]);
                                if ($max > 0 && $max < 999999999) {
                                    $subQ->whereRaw('COALESCE(sale_price, price) <= ?', [$max]);
                                }
                            });
                        }
                    }
                });
            }
        } else {
            if ($request->filled('min_price')) {
                $query->whereRaw('COALESCE(sale_price, price) >= ?', [$request->input('min_price')]);
            }
            if ($request->filled('max_price')) {
                $query->whereRaw('COALESCE(sale_price, price) <= ?', [$request->input('max_price')]);
            }
        }

        $sort = $request->input('sort', 'featured');
        switch ($sort) {
            case 'price-asc':
                $query->orderByRaw('COALESCE(sale_price, price) asc');
                break;
            case 'price-desc':
                $query->orderByRaw('COALESCE(sale_price, price) desc');
                break;
            case 'newest':
                $query->orderBy('published_at', 'desc')->orderBy('id', 'desc');
                break;
            case 'discount':
                $query->orderByRaw('(CASE WHEN sale_price > 0 AND sale_price < price THEN (price - sale_price) ELSE 0 END) desc');
                break;
            case 'bestseller':
                $query->orderBy('reviews_count', 'desc')->orderBy('id', 'desc');
                break;
            case 'name':
                $query->orderBy('name', 'asc');
                break;
            case 'featured':
            default:
                $query->orderBy('id', 'desc');
                break;
        }

        return $query;
    }

    public function show(Request $request, $slug)
    {
        $category = Category::where('slug', $slug)->firstOrFail();
        $allCatIds = $category->getAllChildrenIds();
        
        // Force the category filter for this page
        $request->merge(['category' => $slug]);
        
        $parentCategories = ShopController::getSidebarCategories();
        
        $brandsData = Cache::remember("category_brands_{$slug}", 3600, function () use ($allCatIds) {
            return Brand::select(['id', 'name', 'slug', 'logo'])
                ->withCount(['products' => function ($q) use ($allCatIds) {
                    $q->published()->whereIn('category_id', $allCatIds);
                }])
                ->orderBy('name', 'asc')
                ->get()
                ->toArray();
        });
        $brands = collect($brandsData)->map(function ($item) {
            return (object)$item;
        });
        
        $query = $this->buildProductQuery($request);
        $totalProducts = (clone $query)->count();
        $products = $query->take(24)->get();
        
        $maxPrice = ShopController::getMaxPrice();
        
        $viewedProductIds = session()->get('viewed_products', []);
        $viewedProducts = [];
        if (!empty($viewedProductIds)) {
            $placeholders = implode(',', array_fill(0, count($viewedProductIds), '?'));
            $viewedProducts = Product::published()
                ->select(ShopController::PRODUCT_CARD_COLUMNS)
                ->with('thumbnailMedia')
                ->whereIn('id', $viewedProductIds)
                ->orderByRaw("FIELD(id, $placeholders)", $viewedProductIds)
                ->take(8)
                ->get();
        }

        return view('clients.pages.categories.show', compact('category', 'parentCategories', 'brands', 'products', 'totalProducts', 'maxPrice', 'viewedProducts'));
    }
}
