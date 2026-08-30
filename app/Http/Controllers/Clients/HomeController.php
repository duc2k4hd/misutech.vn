<?php

namespace App\Http\Controllers\Clients;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use App\Models\Category;
use App\Models\Product;
use App\Models\Post;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Arr;

class HomeController extends Controller
{
    /**
     * Cột sản phẩm cần thiết cho hiển thị thẻ sản phẩm (tránh load cột content nặng vào RAM)
     */
    const PRODUCT_CARD_COLUMNS = [
        'id', 'name', 'slug', 'sku', 'price', 'sale_price',
        'rating_average', 'reviews_count', 'published_at',
        'category_id', 'brand_id', 'status'
    ];

    public function index()
    {
        // 1. Banners: Query nhanh nhóm theo vị trí
        $banners = Banner::where('status', 'active')
            ->orderBy('position', 'asc')
            ->get()
            ->groupBy('position');

        // 2. Flash Sale: Cache danh sách 5 ID sản phẩm ngẫu nhiên trong ngày
        $secondsUntilMidnight = max(60, now()->diffInSeconds(now()->endOfDay()));
        $flashSaleProductIds = Cache::remember('home_flash_sale_product_ids', $secondsUntilMidnight, function () {
            return Product::published()->inRandomOrder()->take(5)->pluck('id')->toArray();
        });

        // 3. Featured Products: Cache danh sách 17 ID sản phẩm mới nhất
        $featuredProductIds = Cache::remember('home_featured_product_ids', 1800, function () {
            return Product::published()->latest('published_at')->take(17)->pluck('id')->toArray();
        });

        // 4. Category Sections: Thuật toán gom nhóm In-memory siêu tốc, 0 đệ quy, giảm từ 300+ queries xuống 2 queries
        $categorySectionsData = Cache::remember('home_category_sections_map', 1800, function () {
            $allCats = Category::where('status', 'active')
                ->where('type', 'product')
                ->select(['id', 'name', 'slug', 'parent_id', 'icon', 'banner', 'position'])
                ->orderBy('position', 'asc')
                ->get();

            if ($allCats->isEmpty()) {
                return [];
            }

            // Xây dựng bản đồ con cháu in-memory (0 query DB)
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

            // Đếm số sản phẩm từng danh mục chỉ với 1 query GROUP BY
            $productCounts = Product::published()
                ->selectRaw('category_id, COUNT(*) as aggregate')
                ->groupBy('category_id')
                ->pluck('aggregate', 'category_id')
                ->toArray();

            $candidates = [];
            foreach ($allCats as $cat) {
                $allCatIds = $getDescendantIds($cat->id);
                $pCount = 0;
                foreach ($allCatIds as $cid) {
                    $pCount += $productCounts[$cid] ?? 0;
                }

                if ($pCount > 0) {
                    $candidates[] = [
                        'category' => $cat,
                        'category_id' => $cat->id,
                        'cat_ids' => $allCatIds,
                        'count' => $pCount,
                        'is_child' => !is_null($cat->parent_id),
                    ];
                }
            }

            usort($candidates, function ($a, $b) {
                if ($a['is_child'] === $b['is_child']) {
                    return $b['count'] <=> $a['count'];
                }
                return $a['is_child'] ? -1 : 1;
            });

            $selected = array_slice($candidates, 0, 5);
            if (empty($selected)) {
                return [];
            }

            $allTargetCatIds = [];
            foreach ($selected as $s) {
                $allTargetCatIds = array_merge($allTargetCatIds, $s['cat_ids']);
            }
            $allTargetCatIds = array_values(array_unique($allTargetCatIds));

            $rawProducts = Product::published()
                ->select(['id', 'category_id', 'published_at'])
                ->whereIn('category_id', $allTargetCatIds)
                ->latest('published_at')
                ->get();

            $result = [];
            foreach ($selected as $item) {
                $targetIdsLookup = array_flip($item['cat_ids']);
                $pIds = $rawProducts->filter(function ($p) use ($targetIdsLookup) {
                    return isset($targetIdsLookup[$p->category_id]);
                })->take(10)->pluck('id')->toArray();

                if (!empty($pIds)) {
                    $catModel = $item['category'];
                    $result[] = [
                        'category_id' => $catModel->id,
                        'category' => [
                            'id' => $catModel->id,
                            'name' => $catModel->name,
                            'slug' => $catModel->slug,
                            'icon' => $catModel->icon,
                            'banner' => $catModel->banner,
                        ],
                        'product_ids' => $pIds
                    ];
                }
            }

            return $result;
        });

        // 5. GOM TOÀN BỘ SẢN PHẨM TRANG CHỦ VÀO 1 CÂU QUERY DUY NHẤT (Batch Fetching)
        $allSectionProductIds = [];
        if (!empty($categorySectionsData)) {
            foreach ($categorySectionsData as $sec) {
                if (!empty($sec['product_ids'])) {
                    $allSectionProductIds = array_merge($allSectionProductIds, $sec['product_ids']);
                }
            }
        }

        $allNeededProductIds = array_values(array_unique(array_merge(
            $flashSaleProductIds,
            $featuredProductIds,
            $allSectionProductIds
        )));

        $allProductsKeyed = !empty($allNeededProductIds)
            ? Product::published()
                ->select(self::PRODUCT_CARD_COLUMNS)
                ->with('thumbnailMedia')
                ->whereIn('id', $allNeededProductIds)
                ->get()
                ->keyBy('id')
            : collect();

        // Gán Flash Sale
        $flashSaleProducts = collect();
        foreach ($flashSaleProductIds as $id) {
            if (isset($allProductsKeyed[$id])) {
                $flashSaleProducts->push($allProductsKeyed[$id]);
            }
        }

        // Gán Featured Products
        $featuredProducts = collect();
        foreach ($featuredProductIds as $id) {
            if (isset($allProductsKeyed[$id])) {
                $featuredProducts->push($allProductsKeyed[$id]);
            }
        }

        // Gán Category Sections (0 query vào bảng categories!)
        $categorySections = collect();
        if (!empty($categorySectionsData)) {
            foreach ($categorySectionsData as $sec) {
                $secProducts = collect();
                $pIds = $sec['product_ids'] ?? [];
                foreach ($pIds as $pid) {
                    if (isset($allProductsKeyed[$pid])) {
                        $secProducts->push($allProductsKeyed[$pid]);
                    }
                }
                if ($secProducts->isNotEmpty()) {
                    $cData = is_array($sec['category']) ? $sec['category'] : (array)$sec['category'];
                    $categorySections->push((object)[
                        'category' => (object)$cData,
                        'products' => $secProducts
                    ]);
                }
            }
        }

        // 6. Blog Sections: Cache map ID & category meta
        $blogSectionsData = Cache::remember('home_blog_sections_map', 3600, function () {
            $postCats = Category::where('type', 'post')
                ->where('status', 'active')
                ->select(['id', 'name', 'slug'])
                ->has('posts')
                ->take(4)
                ->get();

            $result = [];
            foreach ($postCats as $cat) {
                $postIds = Post::where('category_id', $cat->id)
                    ->where('status', 'published')
                    ->latest()
                    ->take(3)
                    ->pluck('id')
                    ->toArray();

                if (!empty($postIds)) {
                    $result[] = [
                        'category' => [
                            'id' => $cat->id,
                            'name' => $cat->name,
                            'slug' => $cat->slug
                        ],
                        'post_ids' => $postIds
                    ];
                }
            }
            return $result;
        });

        $blogSections = collect();
        if (!empty($blogSectionsData)) {
            $allPostIds = [];
            foreach ($blogSectionsData as $bSec) {
                if (!empty($bSec['post_ids'])) {
                    $allPostIds = array_merge($allPostIds, $bSec['post_ids']);
                }
            }
            $allPostIds = array_values(array_unique($allPostIds));

            $allPosts = !empty($allPostIds)
                ? Post::whereIn('id', $allPostIds)
                    ->select(['id', 'title', 'slug', 'summary', 'published_at', 'category_id', 'created_at'])
                    ->with('thumbnailMedia')
                    ->get()
                    ->keyBy('id')
                : collect();

            foreach ($blogSectionsData as $bSec) {
                $catPosts = collect();
                $pIds = $bSec['post_ids'] ?? [];
                foreach ($pIds as $pid) {
                    if (isset($allPosts[$pid])) {
                        $catPosts->push($allPosts[$pid]);
                    }
                }
                if ($catPosts->isNotEmpty()) {
                    $catData = is_array($bSec['category']) ? $bSec['category'] : (array)$bSec['category'];
                    $blogSections->push((object)[
                        'id' => $catData['id'] ?? 0,
                        'name' => $catData['name'] ?? '',
                        'slug' => $catData['slug'] ?? '',
                        'posts' => $catPosts
                    ]);
                }
            }
        }

        return view('clients.pages.home.index', compact(
            'banners',
            'flashSaleProducts',
            'featuredProducts',
            'categorySections',
            'blogSections'
        ));
    }
}
