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
            $brands = is_array($request->input('brands')) ? $request->input('brands') : explode(',', $request->input('brands'));
            $query->whereHas('brand', function ($q) use ($brands) {
                $q->whereIn('slug', $brands);
            });
        }

        if ($request->filled('min_price')) {
            $query->whereRaw('COALESCE(sale_price, price) >= ?', [$request->input('min_price')]);
        }
        if ($request->filled('max_price')) {
            $query->whereRaw('COALESCE(sale_price, price) <= ?', [$request->input('max_price')]);
        }

        $sort = $request->input('sort', 'featured');
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
        $products = $query->take(21)->get();
        
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
