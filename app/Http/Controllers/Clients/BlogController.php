<?php

namespace App\Http\Controllers\Clients;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\Category;
use Illuminate\Support\Facades\Cache;

class BlogController extends Controller
{
    /**
     * Danh sách cột bài viết cần thiết cho hiển thị thẻ / danh sách (tránh nạp trường content HTML lớn vào RAM)
     */
    const POST_CARD_COLUMNS = [
        'id', 'title', 'slug', 'summary', 'published_at',
        'category_id', 'author_id', 'views_count', 'created_at'
    ];

    /**
     * Lấy danh sách chuyên mục bài viết bên Sidebar (Cached)
     */
    public static function getSidebarCategories()
    {
        $categoriesData = Cache::remember('blog_sidebar_categories', 3600, function () {
            $cats = Category::where('status', 'active')
                ->where('type', 'post')
                ->whereNull('parent_id')
                ->select(['id', 'name', 'slug', 'parent_id', 'position'])
                ->orderBy('position', 'asc')
                ->with(['children' => function ($c) {
                    $c->where('status', 'active')
                      ->select(['id', 'name', 'slug', 'parent_id', 'position'])
                      ->withCount(['posts' => function ($p) {
                          $p->where('status', 'published');
                      }]);
                }])
                ->withCount(['posts' => function ($p) {
                    $p->where('status', 'published');
                }])
                ->get();

            return $cats->map(function ($cat) {
                return [
                    'id' => $cat->id,
                    'name' => $cat->name,
                    'slug' => $cat->slug,
                    'posts_count' => $cat->posts_count,
                    'children' => $cat->children->map(function ($child) {
                        return [
                            'id' => $child->id,
                            'name' => $child->name,
                            'slug' => $child->slug,
                            'posts_count' => $child->posts_count,
                        ];
                    })->toArray()
                ];
            })->toArray();
        });

        return collect($categoriesData)->map(function ($item) {
            $obj = (object)$item;
            $obj->children = collect($item['children'] ?? [])->map(function ($c) {
                return (object)$c;
            });
            return $obj;
        });
    }

    /**
     * Lấy danh sách bài viết xem nhiều nhất (Cached ID)
     */
    public static function getPopularPosts()
    {
        $postIds = Cache::remember('blog_popular_post_ids', 1800, function () {
            return Post::where('status', 'published')
                ->orderBy('views_count', 'desc')
                ->limit(5)
                ->pluck('id')
                ->toArray();
        });

        if (empty($postIds)) {
            return collect();
        }

        $postsKeyed = Post::select(self::POST_CARD_COLUMNS)
            ->with(['thumbnailMedia'])
            ->whereIn('id', $postIds)
            ->get()
            ->keyBy('id');

        $result = collect();
        foreach ($postIds as $id) {
            if (isset($postsKeyed[$id])) {
                $result->push($postsKeyed[$id]);
            }
        }
        return $result;
    }

    /**
     * Lấy danh sách bài viết mới nhất cho Sidebar (Cached ID)
     */
    public static function getRecentPosts($excludeId = null)
    {
        $postIds = Cache::remember('blog_recent_post_ids', 1800, function () {
            return Post::where('status', 'published')
                ->latest('published_at')
                ->limit(6)
                ->pluck('id')
                ->toArray();
        });

        if (empty($postIds)) {
            return collect();
        }

        $postsKeyed = Post::select(self::POST_CARD_COLUMNS)
            ->with(['thumbnailMedia'])
            ->whereIn('id', $postIds)
            ->get()
            ->keyBy('id');

        $result = collect();
        foreach ($postIds as $id) {
            if ($excludeId && $id == $excludeId) {
                continue;
            }
            if (isset($postsKeyed[$id])) {
                $result->push($postsKeyed[$id]);
            }
            if ($result->count() >= 5) {
                break;
            }
        }
        return $result;
    }

    /**
     * Danh sách bài viết tin tức / cẩm nang kỹ thuật.
     */
    public function index(Request $request)
    {
        $search = trim($request->input('q', ''));
        $catSlug = $request->input('category');

        $query = Post::query()
            ->where('status', 'published')
            ->select(self::POST_CARD_COLUMNS)
            ->with([
                'category:id,name,slug',
                'thumbnailMedia',
                'author:id,name'
            ]);

        // Tìm kiếm
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('summary', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%")
                  ->orWhereHas('category', function ($c) use ($search) {
                      $c->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // Lọc theo danh mục (In-memory mapping)
        $selectedCategory = null;
        if (!empty($catSlug)) {
            $descMap = Category::getDescendantsMap('post');
            $catId = $descMap['slug_to_id'][$catSlug] ?? null;

            if ($catId) {
                $selectedCategory = Category::where('id', $catId)->first();
                if (isset($descMap['descendants'][$catId])) {
                    $query->whereIn('category_id', $descMap['descendants'][$catId]);
                }
            }
        }

        // Bài viết nổi bật (Featured / mới nhất)
        $featuredPost = null;
        if (empty($search) && empty($catSlug) && $request->get('page', 1) == 1) {
            $featuredPost = (clone $query)->orderBy('views_count', 'desc')->orderBy('id', 'desc')->first();
            if ($featuredPost) {
                $query->where('id', '!=', $featuredPost->id);
            }
        }

        $posts = $query->orderBy('published_at', 'desc')->orderBy('id', 'desc')->paginate(9)->withQueryString();

        // Danh sách chuyên mục bài viết (Cached)
        $categories = self::getSidebarCategories();

        // Bài viết xem nhiều nhất (Cached)
        $popularPosts = self::getPopularPosts();

        return view('clients.pages.blogs.index', compact(
            'posts',
            'featuredPost',
            'categories',
            'popularPosts',
            'search',
            'catSlug',
            'selectedCategory'
        ));
    }

    /**
     * Chi tiết bài viết.
     */
    public function show($slug)
    {
        $post = Post::where('slug', $slug)
            ->where('status', 'published')
            ->with(['category:id,name,slug,parent_id', 'thumbnailMedia', 'author:id,name'])
            ->firstOrFail();

        // Bài viết liên quan (In-memory mapping)
        $relatedCategoryIds = [];
        if ($post->category_id) {
            $descMap = Category::getDescendantsMap('post');
            if (isset($descMap['descendants'][$post->category_id])) {
                $relatedCategoryIds = $descMap['descendants'][$post->category_id];
            } else {
                $relatedCategoryIds = [$post->category_id];
            }
        }

        $relatedPosts = Post::where('status', 'published')
            ->select(self::POST_CARD_COLUMNS)
            ->where('id', '!=', $post->id)
            ->when(!empty($relatedCategoryIds), function ($q) use ($relatedCategoryIds) {
                $q->whereIn('category_id', $relatedCategoryIds);
            })
            ->with(['thumbnailMedia', 'category:id,name,slug'])
            ->latest('published_at')
            ->limit(4)
            ->get();

        // Nếu chưa đủ 4 bài, bù thêm bài viết mới nhất khác
        if ($relatedPosts->count() < 4) {
            $needed = 4 - $relatedPosts->count();
            $excludeIds = $relatedPosts->pluck('id')->push($post->id)->toArray();

            $fallbackPosts = Post::where('status', 'published')
                ->select(self::POST_CARD_COLUMNS)
                ->whereNotIn('id', $excludeIds)
                ->with(['thumbnailMedia', 'category:id,name,slug'])
                ->latest('published_at')
                ->limit($needed)
                ->get();

            $relatedPosts = $relatedPosts->concat($fallbackPosts);
        }

        // Bài viết mới nhất cho Sidebar (Cached)
        $recentPosts = self::getRecentPosts($post->id);

        // Danh sách chuyên mục bài viết (Cached)
        $categories = self::getSidebarCategories();

        return view('clients.pages.blogs.show', compact(
            'post',
            'relatedPosts',
            'recentPosts',
            'categories'
        ));
    }
}
