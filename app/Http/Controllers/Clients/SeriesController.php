<?php

namespace App\Http\Controllers\Clients;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Series;
use Illuminate\Http\Request;

class SeriesController extends Controller
{
    /**
     * Danh sách tất cả các Dòng sản phẩm (Series Hub) chuẩn SEO.
     */
    public function index(Request $request)
    {
        $search = trim($request->input('q', ''));
        $brandSlug = $request->input('brand');
        $catSlug = $request->input('category');

        $query = Series::query()
            ->where('status', 'active')
            ->with(['brand:id,name,slug', 'category:id,name,slug'])
            ->withCount(['products' => fn($q) => $q->published()]);

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhereHas('brand', fn($b) => $b->where('name', 'like', "%{$search}%"))
                  ->orWhereHas('category', fn($c) => $c->where('name', 'like', "%{$search}%"));
            });
        }

        if (!empty($brandSlug)) {
            $query->whereHas('brand', fn($b) => $b->where('slug', $brandSlug));
        }

        if (!empty($catSlug)) {
            $query->whereHas('category', fn($c) => $c->where('slug', $catSlug));
        }

        $seriesList = $query->orderBy('name', 'asc')->paginate(24)->withQueryString();

        // Danh sách Thương hiệu & Danh mục để lọc (Cached & Object mapped)
        $brandsData = \Illuminate\Support\Facades\Cache::remember('series_filter_brands', 3600, function () {
            return \App\Models\Brand::select(['id', 'name', 'slug'])
                ->whereHas('series', fn($s) => $s->where('status', 'active'))
                ->orderBy('name', 'asc')
                ->get()
                ->toArray();
        });
        $brands = collect($brandsData)->map(fn($item) => (object)$item);

        $categoriesData = \Illuminate\Support\Facades\Cache::remember('series_filter_categories', 3600, function () {
            return Category::select(['id', 'name', 'slug'])
                ->where('status', 'active')
                ->where('type', 'product')
                ->whereHas('series', fn($s) => $s->where('status', 'active'))
                ->orderBy('name', 'asc')
                ->get()
                ->toArray();
        });
        $categories = collect($categoriesData)->map(fn($item) => (object)$item);

        $totalSeriesCount = \Illuminate\Support\Facades\Cache::remember('series_total_count', 3600, function () {
            return Series::where('status', 'active')->count();
        });

        return view('clients.pages.series.index', compact(
            'seriesList',
            'brands',
            'categories',
            'search',
            'brandSlug',
            'catSlug',
            'totalSeriesCount'
        ));
    }

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
                    $q->published()
                      ->select(['id', 'name', 'slug', 'sku', 'price', 'sale_price', 'series_id', 'status', 'published_at', 'short_description'])
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
                    $cleanName = $doc->filename;
                    if (\Illuminate\Support\Str::startsWith($cleanName, ['http://', 'https://']) || \Illuminate\Support\Str::contains($cleanName, '/')) {
                        $cleanName = basename(urldecode($cleanName));
                    }
                    if (!$cleanName || $cleanName === '.' || $cleanName === '/') {
                        $cleanName = 'Catalog / Tài liệu kỹ thuật ' . $series->name;
                    }
                    $allCatalogs->push((object)[
                        'id' => $doc->id,
                        'url' => $doc->url,
                        'download_url' => route('documents.download', $doc->id),
                        'filename' => $cleanName,
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
                ->withCount(['products' => fn($q) => $q->published()])
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
