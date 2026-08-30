<?php

namespace App\Http\Controllers\Admins;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\Http\JsonResponse;

class CategoryController extends Controller
{
    const CSV_COLUMNS = [
        'id',
        'name',
        'slug',
        'parent',
        'type',
        'position',
        'status',
        'content'
    ];

    public function index()
    {
        $allCategories = Category::all();
        $formattedCategories = [];
        foreach ($allCategories as $cat) {
            $path = $cat->name;
            $parent = $cat->parent_id ? $allCategories->firstWhere('id', $cat->parent_id) : null;
            while ($parent) {
                $path = $parent->name . ' > ' . $path;
                $parent = $parent->parent_id ? $allCategories->firstWhere('id', $parent->parent_id) : null;
            }
            $formattedCategories[] = (object) [
                'id' => $cat->id,
                'name' => $path,
                'type' => $cat->type,
            ];
        }
        usort($formattedCategories, fn($a, $b) => strcmp($a->name, $b->name));

        return view('admins.pages.categories.index', [
            'parentCategories' => $formattedCategories
        ]);
    }

    public function apiList()
    {
        $categories = Category::withCount(['children', 'products', 'posts'])
            ->orderBy('position', 'asc')
            ->orderBy('name', 'asc')
            ->get();
        return response()->json(['data' => $categories]);
    }

    public function apiStore(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:product,post',
            'status' => 'required|in:active,draft',
            'position' => 'nullable|integer',
        ]);

        $slug = $request->slug ? Str::slug($request->slug) : Str::slug($request->name);

        $updateData = [
            'name' => $request->name,
            'slug' => $slug,
            'parent_id' => $request->parent_id ?: null,
            'type' => $request->type,
            'position' => $request->position ?: 0,
            'status' => $request->status,
            'content' => $request->content,
        ];

        // Xử lý upload ảnh Banner chuẩn theo tên slug (không thêm bất kỳ ký tự nào phía sau)
        if ($request->hasFile('banner_file')) {
            $file = $request->file('banner_file');
            $ext = strtolower($file->getClientOriginalExtension() ?: 'png');
            if (in_array($ext, ['png', 'jpg', 'jpeg', 'webp', 'svg', 'avif', 'heic', 'heif'])) {
                $filename = $slug . '.' . $ext;
                $destinationPath = public_path('storage/clients/imgs/banners');
                if (!file_exists($destinationPath)) {
                    mkdir($destinationPath, 0755, true);
                }
                // Xóa các file cũ cùng slug khác đuôi nếu có
                foreach (['png', 'jpg', 'jpeg', 'webp', 'svg'] as $oldExt) {
                    $oldPath = $destinationPath . '/' . $slug . '.' . $oldExt;
                    if (file_exists($oldPath) && $oldExt !== $ext) {
                        @unlink($oldPath);
                    }
                }
                $file->move($destinationPath, $filename);
                $updateData['banner'] = $filename;
            }
        } elseif ($request->input('remove_banner') == '1') {
            $updateData['banner'] = null;
        } elseif ($request->has('banner')) {
            $updateData['banner'] = $request->banner ?: null;
        }

        // Xử lý upload ảnh Icon tròn danh mục chuẩn theo tên slug
        if ($request->hasFile('icon_file')) {
            $file = $request->file('icon_file');
            $ext = strtolower($file->getClientOriginalExtension() ?: 'png');
            if (in_array($ext, ['png', 'jpg', 'jpeg', 'webp', 'svg'])) {
                $filename = $slug . '.' . $ext;
                $destinationPath = public_path('storage/clients/imgs/categories');
                if (!file_exists($destinationPath)) {
                    mkdir($destinationPath, 0755, true);
                }
                $file->move($destinationPath, $filename);
                $updateData['icon'] = $filename;
            }
        } elseif ($request->has('icon')) {
            $updateData['icon'] = $request->icon ?: null;
        }

        $category = Category::updateOrCreate(
            ['id' => $request->id],
            $updateData
        );

        return response()->json([
            'success' => true,
            'message' => 'Lưu danh mục thành công!',
            'data' => $category
        ]);
    }

    public function apiShow($id)
    {
        $category = Category::findOrFail($id);
        return response()->json(['success' => true, 'data' => $category]);
    }

    public function apiDestroy($id)
    {
        $category = Category::findOrFail($id);
        
        // Cập nhật parent_id của các danh mục con thành parent_id của danh mục bị xóa
        Category::where('parent_id', $category->id)->update(['parent_id' => $category->parent_id]);

        $category->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Xóa danh mục thành công!'
        ]);
    }

    /**
     * Xuất toàn bộ danh mục ra file CSV chuẩn UTF-8 (mở tiếng Việt đẹp trên Excel).
     */
    public function apiExport(Request $request): StreamedResponse
    {
        $type = $request->input('type'); // 'all', 'product', 'post'

        $response = new StreamedResponse(function () use ($type) {
            $handle = fopen('php://output', 'w');

            // Ghi BOM UTF-8 để mở tiếng Việt có dấu chuẩn đẹp trên Microsoft Excel
            fputs($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));

            // Ghi dòng tiêu đề
            fputcsv($handle, self::CSV_COLUMNS);

            // Nạp toàn bộ danh mục vào bộ nhớ để tra cứu path phân cấp siêu tốc
            $allCats = Category::select(['id', 'name', 'slug', 'parent_id', 'type', 'position', 'status', 'content'])->get();
            $catMap = $allCats->keyBy('id');

            $query = Category::query();
            if ($type && in_array($type, ['product', 'post'])) {
                $query->where('type', $type);
            }
            $query->orderBy('position', 'asc')->orderBy('name', 'asc');

            $query->chunk(1000, function ($categories) use ($handle, $catMap) {
                foreach ($categories as $cat) {
                    // Xây dựng đường dẫn danh mục cha
                    $parentPath = '';
                    if ($cat->parent_id && isset($catMap[$cat->parent_id])) {
                        $p = $catMap[$cat->parent_id];
                        $parentPath = $p->name;
                        $currParentId = $p->parent_id;
                        while ($currParentId && isset($catMap[$currParentId])) {
                            $parentPath = $catMap[$currParentId]->name . ' > ' . $parentPath;
                            $currParentId = $catMap[$currParentId]->parent_id;
                        }
                    }

                    $row = [
                        $cat->id,
                        $cat->name ?? '',
                        $cat->slug ?? '',
                        $parentPath,
                        $cat->type ?? 'product',
                        $cat->position ?? 0,
                        $cat->status ?? 'active',
                        $cat->content ?? '',
                    ];

                    fputcsv($handle, $row);
                }
            });

            fclose($handle);
        });

        $filename = 'categories_export_' . date('Y_m_d_His') . '.csv';
        $response->headers->set('Content-Type', 'text/csv; charset=UTF-8');
        $response->headers->set('Content-Disposition', "attachment; filename=\"{$filename}\"");

        return $response;
    }

    /**
     * Nhập một lô danh mục (Batch Import) cực nhanh, tự động map và tạo cây phân cấp cha con.
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

        // Cache danh mục trong bộ nhớ theo ID, Slug & Name để tối ưu truy vấn
        $existingCategories = Category::all();
        $catById = $existingCategories->keyBy('id');
        $catBySlug = [];
        $catByNameAndParent = [];

        foreach ($existingCategories as $c) {
            $catBySlug[$c->slug] = $c;
            $key = mb_strtolower(trim($c->name)) . '_' . ($c->parent_id ?: 0) . '_' . $c->type;
            $catByNameAndParent[$key] = $c;
        }

        DB::beginTransaction();
        try {
            foreach ($rows as $row) {
                $name = isset($row['name']) ? trim($row['name']) : '';
                if ($name === '') {
                    $stats['skipped']++;
                    continue;
                }

                $rowId = isset($row['id']) && is_numeric($row['id']) && (int)$row['id'] > 0 ? (int)$row['id'] : null;

                $type = (isset($row['type']) && in_array($row['type'], ['product', 'post'])) ? $row['type'] : 'product';
                $status = (isset($row['status']) && in_array($row['status'], ['active', 'draft'])) ? $row['status'] : 'active';
                $position = isset($row['position']) && is_numeric($row['position']) ? (int)$row['position'] : 0;
                $content = $row['content'] ?? null;
                
                $slug = !empty($row['slug']) ? Str::slug($row['slug']) : Str::slug($name);

                // Xử lý danh mục cha (Hỗ trợ cấu trúc "Cha > Con > Cháu")
                $parentId = null;
                $parentStr = isset($row['parent']) ? trim($row['parent']) : '';
                
                if (!empty($parentStr)) {
                    $parts = array_filter(array_map('trim', explode('>', $parentStr)));
                    $currParentId = null;

                    foreach ($parts as $partName) {
                        $pKey = mb_strtolower($partName) . '_' . ($currParentId ?: 0) . '_' . $type;
                        
                        if (isset($catByNameAndParent[$pKey])) {
                            $currParentId = $catByNameAndParent[$pKey]->id;
                        } else {
                            // Tự động tạo danh mục cha nếu chưa có
                            $pSlug = Str::slug($partName);
                            // Tránh trùng slug
                            if (isset($catBySlug[$pSlug])) {
                                $pSlug = $pSlug . '-' . Str::random(4);
                            }

                            $newParent = Category::create([
                                'name' => $partName,
                                'slug' => $pSlug,
                                'parent_id' => $currParentId,
                                'type' => $type,
                                'status' => 'active',
                                'position' => 0
                            ]);

                            $catById->put($newParent->id, $newParent);
                            $catBySlug[$newParent->slug] = $newParent;
                            $catByNameAndParent[$pKey] = $newParent;
                            $currParentId = $newParent->id;
                        }
                    }
                    $parentId = $currParentId;
                }

                // Kiểm tra xem danh mục đã tồn tại chưa: Ưu tiên 1 theo ID, Ưu tiên 2 theo slug hoặc name + parent
                $catKey = mb_strtolower($name) . '_' . ($parentId ?: 0) . '_' . $type;
                $existing = null;

                if ($rowId && $catById->has($rowId)) {
                    $existing = $catById->get($rowId);
                } elseif (isset($catBySlug[$slug])) {
                    $existing = $catBySlug[$slug];
                } elseif (isset($catByNameAndParent[$catKey])) {
                    $existing = $catByNameAndParent[$catKey];
                }

                if ($existing) {
                    if ($mode === 'insert') {
                        $stats['skipped']++;
                        continue;
                    }

                    // Cập nhật
                    $existing->update([
                        'name' => $name,
                        'slug' => $slug,
                        'parent_id' => $parentId,
                        'type' => $type,
                        'position' => $position,
                        'status' => $status,
                        'content' => $content,
                    ]);

                    $stats['updated']++;
                    $catBySlug[$slug] = $existing;
                    $catByNameAndParent[$catKey] = $existing;
                } else {
                    if ($mode === 'update') {
                        $stats['skipped']++;
                        continue;
                    }

                    // Tránh trùng slug nếu có slug giống ở danh mục khác
                    if (isset($catBySlug[$slug])) {
                        $slug = $slug . '-' . Str::random(4);
                    }

                    // Thêm mới
                    $newCat = Category::create([
                        'name' => $name,
                        'slug' => $slug,
                        'parent_id' => $parentId,
                        'type' => $type,
                        'position' => $position,
                        'status' => $status,
                        'content' => $content,
                    ]);

                    $stats['inserted']++;
                    $catBySlug[$slug] = $newCat;
                    $catByNameAndParent[$catKey] = $newCat;
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Nhập lô danh mục thành công!',
                'stats' => $stats
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Category Import Batch Error: ' . $e->getMessage() . "\n" . $e->getTraceAsString());
            return response()->json([
                'success' => false,
                'message' => 'Lỗi xử lý lô: ' . $e->getMessage()
            ], 500);
        }
    }
}
