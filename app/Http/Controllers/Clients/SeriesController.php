<?php

namespace App\Http\Controllers\Clients;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Series;
use Illuminate\Http\Request;

class SeriesController extends Controller
{
    /**
     * Hiển thị trang Landing / Chi tiết Dòng sản phẩm (Series) chuẩn SEO.
     */
    public function show(Request $request, string $slug)
    {
        $series = Series::where('slug', $slug)
            ->where('status', 'active')
            ->with([
                'brand:id,name,slug',
                'category:id,name,slug,parent_id',
                'products' => function ($q) {
                    $q->where('status', 'active')
                      ->select(['id', 'name', 'slug', 'sku', 'price', 'sale_price', 'series_id', 'status', 'short_description'])
                      ->with(['thumbnailMedia', 'catalogMedia'])
                      ->orderBy('name');
                }
            ])
            ->firstOrFail();

        // Xử lý bộ lọc / sắp xếp model trong series nếu có query parameter
        $products = $series->products;
        $sort = $request->query('sort', 'name_asc');
        
        if ($sort === 'price_asc') {
            $products = $products->sortBy(function ($p) {
                return (float) ($p->sale_price && $p->sale_price < $p->price ? $p->sale_price : $p->price);
            })->values();
        } elseif ($sort === 'price_desc') {
            $products = $products->sortByDesc(function ($p) {
                return (float) ($p->sale_price && $p->sale_price < $p->price ? $p->sale_price : $p->price);
            })->values();
        } elseif ($sort === 'name_desc') {
            $products = $products->sortByDesc('name')->values();
        } else {
            $products = $products->sortBy('name')->values();
        }

        // Tính khoảng giá
        $prices = $series->products->map(function ($p) {
            return (float) ($p->sale_price && $p->sale_price < $p->price ? $p->sale_price : $p->price);
        })->filter(fn($p) => $p > 0);

        $minPrice = $prices->min();
        $maxPrice = $prices->max();

        // Thu thập toàn bộ Catalog/Datasheet của Series (không trùng lặp)
        $allCatalogs = collect();
        foreach ($series->products as $p) {
            foreach ($p->catalogMedia as $doc) {
                if ($doc->url && !$allCatalogs->contains('url', $doc->url)) {
                    $allCatalogs->push((object)[
                        'url' => $doc->url,
                        'filename' => $doc->filename ?: 'Catalog / Tài liệu kỹ thuật ' . $series->name,
                        'product_name' => $p->name
                    ]);
                }
            }
        }

        // Các Series liên quan (Cached)
        $relatedData = \Illuminate\Support\Facades\Cache::remember("series_related_{$series->id}", 3600, function () use ($series) {
            return Series::where('status', 'active')
                ->where('id', '!=', $series->id)
                ->where(function ($q) use ($series) {
                    if ($series->brand_id) {
                        $q->where('brand_id', $series->brand_id);
                    }
                    if ($series->category_id) {
                        $q->orWhere('category_id', $series->category_id);
                    }
                })
                ->select(['id', 'name', 'slug', 'brand_id', 'category_id', 'description'])
                ->with(['brand:id,name,slug', 'category:id,name,slug'])
                ->withCount(['products' => fn($q) => $q->where('status', 'active')])
                ->take(6)
                ->get()
                ->toArray();
        });
        $relatedSeries = collect($relatedData)->map(fn($item) => (object)$item);

        // Lấy danh mục cha phân cấp cho breadcrumb (Cached In-memory)
        $categoryBreadcrumbs = collect();
        if ($series->category) {
            $curr = $series->category;
            $allCatsData = \Illuminate\Support\Facades\Cache::remember('all_categories_hierarchy_product', 3600, function () {
                return Category::where('type', 'product')
                    ->where('status', 'active')
                    ->select(['id', 'name', 'slug', 'parent_id'])
                    ->get()
                    ->toArray();
            });
            $allCats = collect($allCatsData)->map(fn($item) => (object)$item);
            while ($curr) {
                $categoryBreadcrumbs->prepend($curr);
                $curr = $curr->parent_id ? $allCats->firstWhere('id', $curr->parent_id) : null;
            }
        }

        // Ảnh đại diện thumbnail của series (lấy từ model đầu tiên có ảnh)
        $seriesThumbnail = null;
        foreach ($series->products as $p) {
            $img = $p->thumbnailMedia->first()?->url;
            if ($img) {
                $seriesThumbnail = $img;
                break;
            }
        }
        if (!$seriesThumbnail) {
            $seriesThumbnail = asset('storage/clients/imgs/products/no-image.png');
        }

        return view('clients.pages.series.show', compact(
            'series',
            'products',
            'minPrice',
            'maxPrice',
            'allCatalogs',
            'relatedSeries',
            'categoryBreadcrumbs',
            'seriesThumbnail',
            'sort'
        ));
    }
}
