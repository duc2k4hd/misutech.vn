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
use Illuminate\Support\Str;

class SeriesController extends Controller
{
    // ─── Views ────────────────────────────────────────────────────────────────

    public function index()
    {
        $brands = Brand::orderBy('name')->get();
        $allCategories = Category::where('status', 'active')->get();

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
            $query = Series::withCount('products')
                ->with(['brand', 'category']);

            $search = $request->input('search.value');
            if (!empty($search)) {
                $query->where('name', 'like', "%{$search}%")
                      ->orWhere('slug', 'like', "%{$search}%");
            }

            $total    = Series::count();
            $filtered = $query->count();

            $perPage = (int) $request->input('length', 10);
            if ($perPage === -1) {
                $perPage = $filtered > 0 ? $filtered : 10;
            } elseif ($perPage <= 0) {
                $perPage = 10;
            }

            $start = max(0, (int) $request->input('start', 0));
            $page  = max(1, (int) ($start / $perPage) + 1);

            $series = $query->orderByDesc('created_at')
                ->paginate($perPage, ['*'], 'page', $page);

            return response()->json([
                'draw'            => intval($request->input('draw', 1)),
                'recordsTotal'    => $total,
                'recordsFiltered' => $filtered,
                'data'            => array_values($series->items()),
            ]);
        } catch (\Throwable $e) {
            \Log::error('Series apiList error: ' . $e->getMessage());
            return response()->json([
                'draw'            => intval($request->input('draw', 1)),
                'recordsTotal'    => 0,
                'recordsFiltered' => 0,
                'data'            => [],
                'error'           => $e->getMessage()
            ]);
        }
    }

    public function apiStore(SeriesStoreRequest $request): JsonResponse
    {
        $data = $request->validated();
        $data['slug'] = $data['slug'] ?: Str::slug($data['name']);
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
        $series = Series::findOrFail($id);
        $data = $request->validated();

        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
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
        Product::where('series_id', $id)->update(['series_id' => null]);

        $series->delete();

        return response()->json([
            'success' => true,
            'message' => 'Đã xóa dòng sản phẩm. Các sản phẩm thuộc dòng này đã được tách ra.',
        ]);
    }

    // ─── Helpers ──────────────────────────────────────────────────────────────

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
