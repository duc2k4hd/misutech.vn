<?php

namespace App\Http\Controllers\Admins;

use App\Http\Controllers\Controller;
use App\Http\Requests\SeriesStoreRequest;
use App\Http\Requests\SeriesUpdateRequest;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\Series;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class SeriesController extends Controller
{
    // ─── Views ────────────────────────────────────────────────────────────────

    public function index()
    {
        // Tự động kiểm tra và chạy migrate nếu bảng/cột chưa có trên server
        $this->ensureSchemaReady();

        $brands = Schema::hasTable('brands') ? Brand::orderBy('name')->get() : collect();
        $allCategories = Schema::hasTable('categories') ? Category::where('status', 'active')->get() : collect();

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
            ];
        }

        usort($formattedCategories, function($a, $b) {
            return strcmp($a->name, $b->name);
        });

        return view('admins.pages.series.index', [
            'brands' => $brands,
            'categories' => $formattedCategories
        ]);
    }

    // ─── API ──────────────────────────────────────────────────────────────────

    public function apiList(Request $request): JsonResponse
    {
        try {
            $this->ensureSchemaReady();

            $query = Series::query();

            if (Schema::hasColumn('products', 'series_id')) {
                $query->withCount('products');
            }

            if (Schema::hasTable('brands')) {
                $query->with('brand:id,name,slug,logo');
            }
            if (Schema::hasTable('categories')) {
                $query->with('category:id,name,slug');
            }

            // Keyword search
            $keyword = trim($request->input('keyword', $request->input('search.value', '')));
            if (!empty($keyword)) {
                $query->where(function ($q) use ($keyword) {
                    $q->where('name', 'like', "%{$keyword}%")
                      ->orWhere('slug', 'like', "%{$keyword}%");

                    if (Schema::hasTable('brands')) {
                        $q->orWhereHas('brand', function ($b) use ($keyword) {
                            $b->where('name', 'like', "%{$keyword}%");
                        });
                    }
                    if (Schema::hasTable('categories')) {
                        $q->orWhereHas('category', function ($c) use ($keyword) {
                            $c->where('name', 'like', "%{$keyword}%");
                        });
                    }
                });
            }

            // Brand filter
            if ($request->filled('brand_id') && $request->brand_id !== 'all') {
                $query->where('brand_id', $request->brand_id);
            }

            // Category filter
            if ($request->filled('category_id') && $request->category_id !== 'all') {
                $query->where('category_id', $request->category_id);
            }

            // Status filter
            if ($request->filled('status') && $request->status !== 'all') {
                $query->where('status', $request->status);
            }

            // Filter type (e.g. has_products / no_products)
            if ($request->filled('filter_type') && $request->filter_type !== 'all') {
                if ($request->filter_type === 'has_products') {
                    $query->has('products');
                } elseif ($request->filter_type === 'no_products') {
                    $query->doesntHave('products');
                }
            }

            // Sorting
            $sort = $request->input('sort', 'latest');
            if ($sort === 'sort_order') {
                $query->orderBy('sort_order', 'asc')->orderByDesc('id');
            } elseif ($sort === 'oldest') {
                $query->orderBy('id', 'asc');
            } elseif ($sort === 'name_asc') {
                $query->orderBy('name', 'asc');
            } elseif ($sort === 'name_desc') {
                $query->orderBy('name', 'desc');
            } elseif ($sort === 'products_desc') {
                $query->orderByDesc('products_count');
            } else {
                $query->orderBy('id', 'desc');
            }

            $total = Schema::hasTable('series') ? Series::count() : 0;
            $filtered = (clone $query)->count();

            $perPage = (int) $request->input('per_page', $request->input('length', 12));
            if ($perPage <= 0) $perPage = 12;

            $page = max(1, (int) $request->input('page', 1));
            if ($request->has('start') && !$request->has('page')) {
                $start = (int) $request->input('start', 0);
                $page  = max(1, (int) ($start / $perPage) + 1);
            }

            $series = $query->paginate($perPage, ['*'], 'page', $page);

            $stats = [
                'total'         => $total,
                'active'        => Schema::hasTable('series') ? Series::where('status', 'active')->count() : 0,
                'with_products' => Schema::hasTable('series') ? Series::has('products')->count() : 0,
                'total_brands'  => Schema::hasTable('brands') ? Brand::has('series')->count() : 0,
            ];

            return response()->json([
                'draw'            => intval($request->input('draw', 1)),
                'recordsTotal'    => $total,
                'recordsFiltered' => $filtered,
                'data'            => array_values($series->items()),
                'stats'           => $stats,
                'pagination'      => [
                    'current_page' => $series->currentPage(),
                    'last_page'    => $series->lastPage(),
                    'per_page'     => $series->perPage(),
                    'total'        => $series->total(),
                    'from'         => $series->firstItem(),
                    'to'           => $series->lastItem(),
                ]
            ]);
        } catch (\Throwable $e) {
            Log::error('Series apiList error: ' . $e->getMessage() . "\n" . $e->getTraceAsString());
            return response()->json([
                'draw'            => intval($request->input('draw', 1)),
                'recordsTotal'    => 0,
                'recordsFiltered' => 0,
                'data'            => [],
                'stats'           => ['total' => 0, 'active' => 0, 'with_products' => 0, 'total_brands' => 0],
                'pagination'      => ['current_page' => 1, 'last_page' => 1, 'total' => 0],
                'error'           => $e->getMessage()
            ], 200);
        }
    }

    public function apiStore(SeriesStoreRequest $request): JsonResponse
    {
        $this->ensureSchemaReady();

        $data = $request->validated();
        $data['slug'] = !empty($data['slug']) ? Str::slug($data['slug']) : Str::slug($data['name']);
        $data['status'] = $data['status'] ?? 'active';
        $data['sort_order'] = $data['sort_order'] ?? 0;

        // Ensure slug uniqueness
        $data['slug'] = $this->uniqueSlug($data['slug']);

        $series = Series::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Tạo dòng sản phẩm thành công!',
            'data'    => $series->load(['brand', 'category']),
        ]);
    }

    public function apiShow($id): JsonResponse
    {
        $series = Series::with(['brand', 'category', 'products'])->find($id);
        if (!$series) {
            return response()->json(['success' => false, 'message' => 'Không tìm thấy dòng sản phẩm']);
        }
        return response()->json(['success' => true, 'data' => $series]);
    }

    public function apiUpdate(SeriesUpdateRequest $request, $id): JsonResponse
    {
        $this->ensureSchemaReady();

        $series = Series::findOrFail($id);
        $data = $request->validated();

        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        } else {
            $data['slug'] = Str::slug($data['slug']);
        }

        // Ensure slug uniqueness (excluding self)
        $data['slug'] = $this->uniqueSlug($data['slug'], $id);

        $series->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Cập nhật dòng sản phẩm thành công!',
            'data'    => $series->fresh()->load(['brand', 'category']),
        ]);
    }

    public function apiDestroy($id): JsonResponse
    {
        $series = Series::findOrFail($id);

        // Nullify series_id on products before deleting
        if (Schema::hasColumn('products', 'series_id')) {
            Product::where('series_id', $id)->update(['series_id' => null]);
        }

        $series->delete();

        return response()->json([
            'success' => true,
            'message' => 'Đã xóa dòng sản phẩm. Các sản phẩm thuộc dòng này đã được tách ra.',
        ]);
    }

    // ─── Helpers ──────────────────────────────────────────────────────────────

    private function ensureSchemaReady(): void
    {
        try {
            if (!Schema::hasTable('series') || !Schema::hasColumn('products', 'series_id')) {
                Artisan::call('migrate', ['--force' => true]);
            }
        } catch (\Throwable $e) {
            Log::warning('Series ensureSchemaReady error: ' . $e->getMessage());
        }
    }

    private function uniqueSlug(string $base, ?int $excludeId = null): string
    {
        $slug  = $base;
        $count = 1;

        $query = Series::where('slug', $slug);
        if ($excludeId) $query->where('id', '!=', $excludeId);

        while ($query->exists()) {
            $slug  = $base . '-' . $count++;
            $query = Series::where('slug', $slug);
            if ($excludeId) $query->where('id', '!=', $excludeId);
        }

        return $slug;
    }
}

