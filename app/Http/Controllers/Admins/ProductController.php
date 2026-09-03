<?php

namespace App\Http\Controllers\Admins;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Series;
use App\Services\Product\ProductService;
use App\Services\Product\ProductImportService;
use App\Http\Requests\ProductStoreRequest;
use App\Http\Requests\ProductUpdateRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    protected ProductService $productService;
    protected ProductImportService $importService;

    public function __construct(ProductService $productService, ProductImportService $importService)
    {
        $this->productService = $productService;
        $this->importService = $importService;
    }

    public function index()
    {
        $categories = Category::where('status', 'active')->get();
        
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
        
        $brands = Brand::all(['id', 'name']);
        $series = Series::where('status', 'active')->orderByDesc('created_at')->get(['id', 'name']);
        
        return view('admins.pages.products.index', [
            'categories' => $formattedCategories,
            'brands' => $brands,
            'series' => $series
        ]);
    }

    public function apiList(Request $request): JsonResponse
    {
        try {
            $trashStatus = $request->input('trash_status', 'all'); // 'all', 'active', 'trashed'
            $query = Product::withTrashed()->with(['category', 'brand', 'series', 'thumbnailMedia']);

            if ($trashStatus === 'active') {
                $query->whereNull('deleted_at');
            } elseif ($trashStatus === 'trashed') {
                $query->onlyTrashed();
            }

            $searchValue = $request->input('search.value');
            if (!empty($searchValue)) {
                $query->where(function($q) use ($searchValue) {
                    $q->where('name', 'like', "%{$searchValue}%")
                      ->orWhere('sku', 'like', "%{$searchValue}%");
                });
            }

            $totalRecords = Product::withTrashed()->count();
            $filteredRecords = $query->count();

            $perPage = (int) $request->input('length', 10);
            if ($perPage === -1) {
                $perPage = $filteredRecords > 0 ? $filteredRecords : 10;
            } elseif ($perPage <= 0) {
                $perPage = 10;
            }

            $start = max(0, (int) $request->input('start', 0));
            $page = max(1, (int) ($start / $perPage) + 1);

            $products = $query->orderBy('id', 'desc')->paginate($perPage, ['*'], 'page', $page);

            // Thêm trường `is_trashed` cho mỗi item
            $items = collect($products->items())->map(function($p) {
                $arr = $p->toArray();
                $arr['is_trashed'] = $p->trashed();
                return $arr;
            })->values()->all();

            return response()->json([
                'draw' => intval($request->input('draw', 1)),
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $filteredRecords,
                'data' => $items,
                'counts' => [
                    'all'     => Product::withTrashed()->count(),
                    'active'  => Product::whereNull('deleted_at')->count(),
                    'trashed' => Product::onlyTrashed()->count(),
                ]
            ]);
        } catch (\Throwable $e) {
            \Log::error('Product apiList error: ' . $e->getMessage() . "\n" . $e->getTraceAsString());
            return response()->json([
                'draw' => intval($request->input('draw', 1)),
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => [],
                'counts' => ['all' => 0, 'active' => 0, 'trashed' => 0],
                'error' => $e->getMessage()
            ]);
        }
    }

    public function apiStore(ProductStoreRequest $request): JsonResponse
    {
        $product = $this->productService->saveProduct($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Tạo sản phẩm thành công!',
            'data' => $product
        ]);
    }

    public function apiUpdate(ProductUpdateRequest $request, $id): JsonResponse
    {
        $product = $this->productService->saveProduct($request->validated(), $id);

        return response()->json([
            'success' => true,
            'message' => 'Cập nhật sản phẩm thành công!',
            'data' => $product
        ]);
    }

    public function apiShow($id): JsonResponse
    {
        $product = Product::withTrashed()->with(['thumbnailMedia', 'galleryMedia', 'catalogMedia'])->find($id);
        
        if ($product) {
            // Flatten media for easy frontend consumption
            $data = $product->toArray();
            $data['thumbnail_id'] = $product->thumbnailMedia->first()->id ?? null;
            $data['thumbnail_url'] = $product->thumbnailMedia->first()->url ?? null;
            
            $data['gallery'] = $product->galleryMedia->map(function($media) {
                return [
                    'id' => $media->id,
                    'url' => $media->url,
                ];
            });
            
            $data['catalog'] = $product->catalogMedia->map(function($media) {
                return [
                    'id' => $media->id,
                    'url' => $media->url,
                    'filename' => $media->filename,
                ];
            });

            return response()->json(['success' => true, 'data' => $data]);
        }
        return response()->json(['success' => false, 'message' => 'Không tìm thấy sản phẩm']);
    }

    public function apiDestroy($id): JsonResponse
    {
        $product = Product::find($id);
        if ($product) {
            $product->delete();
            return response()->json(['success' => true, 'message' => 'Đã chuyển sản phẩm vào thùng rác (xóa mềm)!']);
        }
        return response()->json(['success' => false, 'message' => 'Không tìm thấy sản phẩm']);
    }

    /**
     * Restore a soft-deleted product.
     */
    public function apiRestore($id): JsonResponse
    {
        $product = Product::onlyTrashed()->find($id);
        if ($product) {
            $product->restore();
            return response()->json(['success' => true, 'message' => 'Khôi phục sản phẩm thành công!']);
        }
        return response()->json(['success' => false, 'message' => 'Không tìm thấy sản phẩm trong thùng rác']);
    }

    /**
     * Force delete a product permanently (including physical files & media records).
     */
    public function apiForceDelete($id): JsonResponse
    {
        $product = Product::withTrashed()->find($id);
        if (!$product) {
            return response()->json(['success' => false, 'message' => 'Không tìm thấy sản phẩm']);
        }

        // Lấy danh sách media_id liên kết với sản phẩm này
        $mediaIds = DB::table('product_media')
            ->where('product_id', $id)
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
            DB::table('product_media')->where('product_id', $id)->delete();
        }

        $product->forceDelete();
        return response()->json(['success' => true, 'message' => 'Đã xóa vĩnh viễn sản phẩm và ảnh liên quan!']);
    }

    /**
     * Delete multiple selected products (Soft Delete).
     */
    public function apiBulkDestroy(Request $request): JsonResponse
    {
        @set_time_limit(300);
        $ids = $request->input('ids', []);
        if (empty($ids) || !is_array($ids)) {
            return response()->json(['success' => false, 'message' => 'Vui lòng chọn ít nhất một sản phẩm để xóa!']);
        }
        
        $count = 0;
        collect($ids)->chunk(1000)->each(function ($chunkIds) use (&$count) {
            $count += Product::whereIn('id', $chunkIds)->delete();
        });

        return response()->json([
            'success' => true, 
            'message' => "Đã chuyển thành công {$count} sản phẩm vào thùng rác!",
            'count' => $count
        ]);
    }

    /**
     * Restore multiple selected products.
     */
    public function apiBulkRestore(Request $request): JsonResponse
    {
        @set_time_limit(300);
        $ids = $request->input('ids', []);
        if (empty($ids) || !is_array($ids)) {
            return response()->json(['success' => false, 'message' => 'Vui lòng chọn ít nhất một sản phẩm để khôi phục!']);
        }
        
        $count = 0;
        collect($ids)->chunk(1000)->each(function ($chunkIds) use (&$count) {
            $count += Product::onlyTrashed()->whereIn('id', $chunkIds)->restore();
        });

        return response()->json([
            'success' => true, 
            'message' => "Đã khôi phục thành công {$count} sản phẩm!",
            'count' => $count
        ]);
    }

    /**
     * Force delete multiple selected products permanently (Fast bulk with media + file cleanup).
     */
    public function apiBulkForceDelete(Request $request): JsonResponse
    {
        @set_time_limit(600);
        $ids = $request->input('ids', []);
        if (empty($ids) || !is_array($ids)) {
            return response()->json(['success' => false, 'message' => 'Vui lòng chọn ít nhất một sản phẩm để xóa vĩnh viễn!']);
        }

        $count = 0;
        collect($ids)->chunk(500)->each(function ($chunkIds) use (&$count) {
            // 1. Lấy toàn bộ media_id liên kết với nhóm sản phẩm này
            $mediaIds = DB::table('product_media')
                ->whereIn('product_id', $chunkIds)
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

            // 4. Xóa pivot product_media
            DB::table('product_media')->whereIn('product_id', $chunkIds)->delete();

            // 5. Xóa vĩnh viễn sản phẩm
            $count += Product::withTrashed()->whereIn('id', $chunkIds)->forceDelete();
        });

        return response()->json([
            'success' => true,
            'message' => "Đã xóa vĩnh viễn thành công {$count} sản phẩm và toàn bộ ảnh liên quan!",
            'count'   => $count
        ]);
    }

    /**
     * Export all products to CSV.
     */
    public function apiExport(Request $request)
    {
        $type = $request->input('type', 'all'); // 'all', 'selected', 'category'
        $ids = $request->input('ids'); // array of ids or comma string
        if (is_string($ids)) {
            $ids = array_filter(explode(',', $ids));
        }
        $categoryId = $request->input('category_id');

        return $this->importService->exportCsv($type, $ids, $categoryId);
    }

    /**
     * Import a batch of products.
     */
    public function apiImportBatch(Request $request): JsonResponse
    {
        @set_time_limit(0);
        @ini_set('max_execution_time', '0');
        @ini_set('memory_limit', '512M');

        $rows = $request->input('rows', []);
        $mode = $request->input('mode', 'upsert');
        
        if (empty($rows)) {
            return response()->json(['success' => false, 'message' => 'Dữ liệu trống']);
        }

        try {
            $stats = $this->importService->importBatch($rows, $mode);
            return response()->json([
                'success' => true,
                'message' => 'Import lô thành công',
                'stats' => $stats
            ]);
        } catch (\Exception $e) {
            \Log::error('Import Batch Error: ' . $e->getMessage() . "\n" . $e->getTraceAsString());
            return response()->json([
                'success' => false,
                'message' => 'Lỗi xử lý lô: ' . $e->getMessage()
            ], 500);
        }
    }
}
