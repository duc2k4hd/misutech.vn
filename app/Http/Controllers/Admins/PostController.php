<?php

namespace App\Http\Controllers\Admins;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\Category;
use App\Models\Media;
use App\Services\Post\PostService;
use App\Queries\AdminPostQuery;
use App\Http\Requests\PostStoreRequest;
use App\Http\Requests\PostUpdateRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PostController extends Controller
{
    protected PostService $postService;
    protected AdminPostQuery $postQuery;

    const CSV_COLUMNS = [
        'id',
        'title',
        'slug',
        'category',
        'summary',
        'content',
        'status',
        'meta_title',
        'meta_description',
        'image'
    ];

    public function __construct(PostService $postService, AdminPostQuery $postQuery)
    {
        $this->postService = $postService;
        $this->postQuery = $postQuery;
    }

    public function index()
    {
        $categories = Category::where('status', 'active')->where('type', 'post')->get();
        // Format categories hierarchically for the dropdown
        $formattedCategories = [];
        foreach ($categories as $cat) {
            $path = $cat->name;
            $parent = $cat->parent_id ? $categories->firstWhere('id', $cat->parent_id) : null;
            while ($parent) {
                $path = $parent->name . ' > ' . $path;
                $parent = $parent->parent_id ? $categories->firstWhere('id', $parent->parent_id) : null;
            }
            $formattedCategories[] = (object) [
                'id' => $cat->id,
                'name' => $path,
            ];
        }
        
        usort($formattedCategories, function($a, $b) {
            return strcmp($a->name, $b->name);
        });

        return view('admins.pages.posts.index', [
            'categories' => $formattedCategories
        ]);
    }

    public function apiList(Request $request): JsonResponse
    {
        return response()->json($this->postQuery->getDatatables($request));
    }

    public function apiStore(PostStoreRequest $request): JsonResponse
    {
        $data = $request->validated();
        $data['author_id'] = auth()->id() ?? 1;

        $post = $this->postService->savePost($data);

        return response()->json([
            'success' => true,
            'message' => 'Tạo bài viết thành công!',
            'data' => $post
        ]);
    }

    public function apiUpdate(PostUpdateRequest $request, $id): JsonResponse
    {
        $data = $request->validated();
        $post = $this->postService->savePost($data, $id);

        return response()->json([
            'success' => true,
            'message' => 'Cập nhật bài viết thành công!',
            'data' => $post
        ]);
    }

    public function apiShow($id): JsonResponse
    {
        $post = Post::with(['thumbnailMedia', 'contentMedia'])->find($id);
        
        if ($post) {
            $data = $post->toArray();
            $data['thumbnail_id'] = $post->thumbnailMedia->first()->id ?? null;
            $data['thumbnail_url'] = $post->thumbnailMedia->first()->url ?? null;
            
            return response()->json(['success' => true, 'data' => $data]);
        }
        return response()->json(['success' => false, 'message' => 'Không tìm thấy bài viết']);
    }

    public function apiDestroy($id): JsonResponse
    {
        $post = Post::find($id);
        if ($post) {
            $post->delete();
            return response()->json(['success' => true, 'message' => 'Đã chuyển bài viết vào thùng rác (xóa mềm)!']);
        }
        return response()->json(['success' => false, 'message' => 'Không tìm thấy bài viết!'], 404);
    }

    /**
     * Restore a soft-deleted post.
     */
    public function apiRestore($id): JsonResponse
    {
        $post = Post::onlyTrashed()->find($id);
        if ($post) {
            $post->restore();
            return response()->json(['success' => true, 'message' => 'Khôi phục bài viết thành công!']);
        }
        return response()->json(['success' => false, 'message' => 'Không tìm thấy bài viết trong thùng rác!'], 404);
    }

    /**
     * Force delete a post permanently (including physical files & media records).
     */
    public function apiForceDelete($id): JsonResponse
    {
        $post = Post::withTrashed()->find($id);
        if (!$post) {
            return response()->json(['success' => false, 'message' => 'Không tìm thấy bài viết!'], 404);
        }

        // Lấy danh sách media_id liên kết với bài viết này
        $mediaIds = DB::table('post_media')
            ->where('post_id', $id)
            ->pluck('media_id')
            ->toArray();

        // Xóa file vật lý và bản ghi media
        if (!empty($mediaIds)) {
            $mediaRecords = \App\Models\Media::whereIn('id', $mediaIds)->get();
            foreach ($mediaRecords as $media) {
                $filePath = public_path('storage/' . $media->relative_path);
                if (file_exists($filePath)) {
                    @unlink($filePath);
                }
            }
            \App\Models\Media::whereIn('id', $mediaIds)->delete();
            // Xóa pivot
            DB::table('post_media')->where('post_id', $id)->delete();
        }

        $post->forceDelete();
        return response()->json(['success' => true, 'message' => 'Đã xóa vĩnh viễn bài viết và ảnh liên quan!']);
    }

    /**
     * Delete multiple selected posts (Soft Delete).
     */
    public function apiBulkDestroy(Request $request): JsonResponse
    {
        @set_time_limit(300);
        $ids = $request->input('ids', []);
        if (empty($ids) || !is_array($ids)) {
            return response()->json(['success' => false, 'message' => 'Vui lòng chọn ít nhất một bài viết để chuyển vào thùng rác!']);
        }
        
        $count = 0;
        collect($ids)->chunk(1000)->each(function ($chunkIds) use (&$count) {
            $count += Post::whereIn('id', $chunkIds)->delete();
        });

        return response()->json([
            'success' => true, 
            'message' => "Đã chuyển thành công {$count} bài viết vào thùng rác!",
            'count' => $count
        ]);
    }

    /**
     * Restore multiple selected posts.
     */
    public function apiBulkRestore(Request $request): JsonResponse
    {
        @set_time_limit(300);
        $ids = $request->input('ids', []);
        if (empty($ids) || !is_array($ids)) {
            return response()->json(['success' => false, 'message' => 'Vui lòng chọn ít nhất một bài viết để khôi phục!']);
        }
        
        $count = 0;
        collect($ids)->chunk(1000)->each(function ($chunkIds) use (&$count) {
            $count += Post::onlyTrashed()->whereIn('id', $chunkIds)->restore();
        });

        return response()->json([
            'success' => true, 
            'message' => "Đã khôi phục thành công {$count} bài viết!",
            'count' => $count
        ]);
    }

    /**
     * Force delete multiple selected posts permanently (Fast bulk with media + file cleanup).
     */
    public function apiBulkForceDelete(Request $request): JsonResponse
    {
        @set_time_limit(600);
        $ids = $request->input('ids', []);
        if (empty($ids) || !is_array($ids)) {
            return response()->json(['success' => false, 'message' => 'Vui lòng chọn ít nhất một bài viết để xóa vĩnh viễn!']);
        }

        $count = 0;
        collect($ids)->chunk(500)->each(function ($chunkIds) use (&$count) {
            // 1. Lấy toàn bộ media_id liên kết với nhóm bài viết này
            $mediaIds = DB::table('post_media')
                ->whereIn('post_id', $chunkIds)
                ->pluck('media_id')
                ->toArray();

            if (!empty($mediaIds)) {
                // 2. Xóa file vật lý khỏi ổ đĩa
                \App\Models\Media::whereIn('id', $mediaIds)
                    ->get(['folder', 'filename'])
                    ->each(function ($media) {
                        $filePath = public_path('storage/' . $media->relative_path);
                        if (file_exists($filePath)) {
                            @unlink($filePath);
                        }
                    });

                // 3. Xóa bản ghi media trong DB
                \App\Models\Media::whereIn('id', $mediaIds)->delete();
            }

            // 4. Xóa pivot post_media
            DB::table('post_media')->whereIn('post_id', $chunkIds)->delete();

            // 5. Xóa vĩnh viễn bài viết
            $count += Post::withTrashed()->whereIn('id', $chunkIds)->forceDelete();
        });

        return response()->json([
            'success' => true,
            'message' => "Đã xóa vĩnh viễn thành công {$count} bài viết và toàn bộ ảnh liên quan!",
            'count'   => $count
        ]);
    }

    /**
     * Xuất danh sách bài viết ra file CSV chuẩn UTF-8.
     * Cho phép chọn cột xuất (bao gồm ID) và chọn danh mục xuất (mặc định xuất tất cả).
     */
    public function apiExport(Request $request): StreamedResponse
    {
        $categoryId = $request->input('category_id');
        $rawColumns = $request->input('columns');

        // Xác định danh sách cột cần xuất
        if (!empty($rawColumns)) {
            $requestedCols = is_array($rawColumns) ? $rawColumns : explode(',', $rawColumns);
            $requestedCols = array_map('trim', $requestedCols);
            $exportColumns = array_values(array_intersect($requestedCols, self::CSV_COLUMNS));
        }

        if (empty($exportColumns)) {
            $exportColumns = self::CSV_COLUMNS; // Mặc định xuất tất cả cột
        }

        $response = new StreamedResponse(function () use ($categoryId, $exportColumns) {
            $handle = fopen('php://output', 'w');

            // Ghi BOM UTF-8 để mở tiếng Việt có dấu chuẩn đẹp trên Microsoft Excel
            fputs($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));

            // Ghi dòng tiêu đề gồm các cột được chọn
            fputcsv($handle, $exportColumns);

            // Cache category map
            $allCats = Category::all()->keyBy('id');

            $query = Post::query()
                ->select(['id', 'title', 'slug', 'category_id', 'summary', 'content', 'status', 'meta_title', 'meta_description'])
                ->with(['category', 'thumbnailMedia']);

            if ($categoryId) {
                // Lấy cả danh mục con của categoryId nếu có
                $descendantIds = [(int)$categoryId];
                foreach ($allCats as $cat) {
                    if ($cat->parent_id == $categoryId) {
                        $descendantIds[] = $cat->id;
                    }
                }
                $query->whereIn('category_id', array_unique($descendantIds));
            }

            $query->orderBy('id', 'desc')->chunk(1000, function ($posts) use ($handle, $allCats, $exportColumns) {
                foreach ($posts as $post) {
                    // Xây dựng đường dẫn danh mục
                    $catPath = '';
                    if ($post->category_id && isset($allCats[$post->category_id])) {
                        $p = $allCats[$post->category_id];
                        $catPath = $p->name;
                        $currParentId = $p->parent_id;
                        while ($currParentId && isset($allCats[$currParentId])) {
                            $catPath = $allCats[$currParentId]->name . ' > ' . $catPath;
                            $currParentId = $allCats[$currParentId]->parent_id;
                        }
                    }

                    $thumb = $post->thumbnailMedia->first();
                    $thumbName = $thumb ? $thumb->filename : '';

                    $dataMap = [
                        'id' => $post->id,
                        'title' => $post->title ?? '',
                        'slug' => $post->slug ?? '',
                        'category' => $catPath,
                        'summary' => $post->summary ?? '',
                        'content' => $post->content ?? '',
                        'status' => $post->status ?? 'published',
                        'meta_title' => $post->meta_title ?? '',
                        'meta_description' => $post->meta_description ?? '',
                        'image' => $thumbName
                    ];

                    $row = [];
                    foreach ($exportColumns as $colKey) {
                        $row[] = $dataMap[$colKey] ?? '';
                    }

                    fputcsv($handle, $row);
                }
            });

            fclose($handle);
        });

        $filename = 'posts_export_' . date('Y_m_d_His') . '.csv';
        $response->headers->set('Content-Type', 'text/csv; charset=UTF-8');
        $response->headers->set('Content-Disposition', "attachment; filename=\"{$filename}\"");

        return $response;
    }

    /**
     * Nhập một lô bài viết (Batch Import) cực nhanh.
     * Hỗ trợ cập nhật chính xác theo ID (giúp đổi slug/title tự do), tự động tạo danh mục bài viết nếu chưa có.
     */
    public function apiImportBatch(Request $request): JsonResponse
    {
        $rows = $request->input('rows', []);
        $mode = $request->input('mode', 'upsert'); // 'upsert', 'insert', 'update'

        if (empty($rows)) {
            return response()->json(['success' => false, 'message' => 'Dữ liệu trống']);
        }

        $stats = [
            'inserted' => 0,
            'updated' => 0,
            'skipped' => 0
        ];

        // Cache categories trong bộ nhớ (lọc type = 'post')
        $existingCategories = Category::where('type', 'post')->get();
        $catBySlug = [];
        $catByNameAndParent = [];

        foreach ($existingCategories as $c) {
            $catBySlug[$c->slug] = $c;
            $key = mb_strtolower(trim($c->name)) . '_' . ($c->parent_id ?: 0);
            $catByNameAndParent[$key] = $c;
        }

        // Cache Media theo filename và original_name
        $mediaFilenames = Media::pluck('id', 'filename')->toArray();
        $mediaOriginals = Media::whereNotNull('original_name')->pluck('id', 'original_name')->toArray();

        // Cache Posts theo ID và Slug
        $allPosts = Post::all();
        $postsById = $allPosts->keyBy('id');
        $postsBySlug = $allPosts->keyBy('slug');

        $defaultAuthorId = auth()->id() ?? 1;

        DB::beginTransaction();
        try {
            foreach ($rows as $row) {
                $title = isset($row['title']) ? trim($row['title']) : '';
                if ($title === '') {
                    $stats['skipped']++;
                    continue;
                }

                $rowId = isset($row['id']) && is_numeric($row['id']) && (int)$row['id'] > 0 ? (int)$row['id'] : null;

                $rawStatus = isset($row['status']) ? strtolower(trim($row['status'])) : 'published';
                $status = in_array($rawStatus, ['draft', 'hidden', '0', 'ẩn']) ? 'draft' : 'published';
                $summary = $row['summary'] ?? null;
                $content = $row['content'] ?? null;
                $metaTitle = $row['meta_title'] ?? null;
                $metaDescription = $row['meta_description'] ?? null;
                
                $slug = !empty($row['slug']) ? Str::slug($row['slug']) : Str::slug($title);

                // Xử lý danh mục bài viết (Tự động tạo mới nếu chưa tồn tại)
                $categoryId = null;
                $catStr = isset($row['category']) ? trim($row['category']) : '';

                if (!empty($catStr)) {
                    $parts = array_filter(array_map('trim', explode('>', $catStr)));
                    $currParentId = null;

                    foreach ($parts as $partName) {
                        $pKey = mb_strtolower($partName) . '_' . ($currParentId ?: 0);

                        if (isset($catByNameAndParent[$pKey])) {
                            $currParentId = $catByNameAndParent[$pKey]->id;
                        } else {
                            // Tự động tạo danh mục bài viết mới
                            $pSlug = Str::slug($partName);
                            if (isset($catBySlug[$pSlug])) {
                                $pSlug = $pSlug . '-' . Str::random(4);
                            }

                            $newCat = Category::create([
                                'name' => $partName,
                                'slug' => $pSlug,
                                'parent_id' => $currParentId,
                                'type' => 'post',
                                'status' => 'active',
                                'position' => 0
                            ]);

                            $catBySlug[$newCat->slug] = $newCat;
                            $catByNameAndParent[$pKey] = $newCat;
                            $currParentId = $newCat->id;
                        }
                    }
                    $categoryId = $currParentId;
                }

                // Xử lý ảnh thumbnail (Nếu có tên file trong kho Media)
                $thumbMediaId = null;
                $imageName = isset($row['image']) ? trim($row['image']) : '';
                if (!empty($imageName)) {
                    if (isset($mediaFilenames[$imageName])) {
                        $thumbMediaId = $mediaFilenames[$imageName];
                    } elseif (isset($mediaOriginals[$imageName])) {
                        $thumbMediaId = $mediaOriginals[$imageName];
                    }
                }

                // Tìm bài viết: Ưu tiên 1 theo ID, Ưu tiên 2 theo Slug
                $existingPost = null;
                if ($rowId && $postsById->has($rowId)) {
                    $existingPost = $postsById->get($rowId);
                } elseif ($postsBySlug->has($slug)) {
                    $existingPost = $postsBySlug->get($slug);
                }

                if ($existingPost) {
                    if ($mode === 'insert') {
                        $stats['skipped']++;
                        continue;
                    }

                    // Cập nhật an toàn: chỉ ghi đè các trường có dữ liệu, giữ nguyên dữ liệu cũ nếu cột bị bỏ qua
                    $updateData = [
                        'title' => $title,
                        'slug' => $slug,
                        'status' => $status,
                    ];

                    if (array_key_exists('category', $row)) {
                        $updateData['category_id'] = $categoryId;
                    }
                    if (array_key_exists('summary', $row) && $row['summary'] !== null) {
                        $updateData['summary'] = $summary;
                    }
                    if (array_key_exists('content', $row) && $row['content'] !== null && $row['content'] !== '') {
                        $updateData['content'] = $content;
                    }
                    if (array_key_exists('meta_title', $row) && $row['meta_title'] !== null) {
                        $updateData['meta_title'] = $metaTitle;
                    }
                    if (array_key_exists('meta_description', $row) && $row['meta_description'] !== null) {
                        $updateData['meta_description'] = $metaDescription;
                    }

                    $existingPost->update($updateData);

                    // Cập nhật thumbnail nếu có
                    if ($thumbMediaId) {
                        $existingPost->thumbnailMedia()->sync([
                            $thumbMediaId => ['role' => 'thumbnail', 'position' => 0]
                        ]);
                    }

                    $stats['updated']++;
                    $postsById->put($existingPost->id, $existingPost);
                    $postsBySlug->put($existingPost->slug, $existingPost);
                } else {
                    if ($mode === 'update') {
                        $stats['skipped']++;
                        continue;
                    }

                    // Tránh trùng slug với bài viết khác
                    if ($postsBySlug->has($slug)) {
                        $slug = $slug . '-' . Str::random(4);
                    }

                    // Thêm mới
                    $newPost = Post::create([
                        'title' => $title,
                        'slug' => $slug,
                        'category_id' => $categoryId,
                        'summary' => $summary ?? '',
                        'content' => $content ?? '',
                        'author_id' => $defaultAuthorId,
                        'status' => $status,
                        'meta_title' => $metaTitle ?? '',
                        'meta_description' => $metaDescription ?? '',
                        'published_at' => now(),
                    ]);

                    if ($thumbMediaId) {
                        $newPost->thumbnailMedia()->sync([
                            $thumbMediaId => ['role' => 'thumbnail', 'position' => 0]
                        ]);
                    }

                    $postsById->put($newPost->id, $newPost);
                    $postsBySlug->put($newPost->slug, $newPost);
                    $stats['inserted']++;
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Nhập lô bài viết thành công!',
                'stats' => $stats
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Post Import Batch Error: ' . $e->getMessage() . "\n" . $e->getTraceAsString());
            return response()->json([
                'success' => false,
                'message' => 'Lỗi xử lý lô: ' . $e->getMessage()
            ], 500);
        }
    }
}
