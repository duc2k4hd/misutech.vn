<?php

namespace App\Http\Controllers\Admins;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BrandController extends Controller
{
    /**
     * Display the brands management page.
     */
    public function index()
    {
        return view('admins.pages.brands.index');
    }

    /**
     * Return JSON data for DataTables.
     */
    public function apiList(Request $request): JsonResponse
    {
        $query = Brand::withCount(['products', 'series']);

        $search = $request->input('search.value');
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('slug', 'like', "%{$search}%");
            });
        }

        $total    = Brand::count();
        $filtered = $query->count();

        $perPage = (int) $request->input('length', 10);
        if ($perPage === -1) $perPage = max($filtered, 1);
        $start   = (int) $request->input('start', 0);
        $page    = (int) ($start / $perPage) + 1;

        $brands = $query->orderByDesc('id')
            ->paginate($perPage, ['*'], 'page', $page);

        return response()->json([
            'draw'            => intval($request->input('draw')),
            'recordsTotal'    => $total,
            'recordsFiltered' => $filtered,
            'data'            => $brands->items(),
        ]);
    }

    /**
     * Store a newly created Brand.
     */
    public function apiStore(Request $request): JsonResponse
    {
        $request->validate([
            'name'             => 'required|string|max:255|unique:brands,name',
            'slug'             => 'nullable|string|max:255|unique:brands,slug',
            'logo'             => 'nullable',
            'content'          => 'nullable|string',
            'meta_title'       => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
        ], [
            'name.required'    => 'Vui lòng nhập tên thương hiệu.',
            'name.unique'      => 'Tên thương hiệu đã tồn tại.',
            'slug.unique'      => 'Đường dẫn tĩnh (slug) đã tồn tại.',
        ]);

        $data = $request->only(['name', 'slug', 'content', 'meta_title', 'meta_description']);
        $data['slug'] = !empty($data['slug']) ? Str::slug($data['slug']) : Str::slug($data['name']);
        $data['slug'] = $this->uniqueSlug($data['slug']);

        // Xử lý upload Logo nếu có
        if ($request->hasFile('logo')) {
            $file = $request->file('logo');
            $filename = 'brand-' . $data['slug'] . '-' . time() . '.' . $file->getClientOriginalExtension();
            $file->storeAs('clients/imgs/brands', $filename, 'public');
            $data['logo'] = $filename;
        } elseif ($request->filled('logo_url')) {
            $data['logo'] = $request->input('logo_url');
        }

        $brand = Brand::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Thêm thương hiệu mới thành công!',
            'data'    => $brand,
        ]);
    }

    /**
     * Display the specified Brand.
     */
    public function apiShow($id): JsonResponse
    {
        $brand = Brand::withCount(['products', 'series'])->findOrFail($id);

        return response()->json([
            'success' => true,
            'data'    => $brand,
        ]);
    }

    /**
     * Update the specified Brand.
     */
    public function apiUpdate(Request $request, $id): JsonResponse
    {
        $brand = Brand::findOrFail($id);

        $request->validate([
            'name'             => 'required|string|max:255|unique:brands,name,' . $id,
            'slug'             => 'nullable|string|max:255|unique:brands,slug,' . $id,
            'content'          => 'nullable|string',
            'meta_title'       => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
        ], [
            'name.required'    => 'Vui lòng nhập tên thương hiệu.',
            'name.unique'      => 'Tên thương hiệu đã tồn tại.',
            'slug.unique'      => 'Đường dẫn tĩnh (slug) đã tồn tại.',
        ]);

        $data = $request->only(['name', 'slug', 'content', 'meta_title', 'meta_description']);
        $data['slug'] = !empty($data['slug']) ? Str::slug($data['slug']) : Str::slug($data['name']);
        $data['slug'] = $this->uniqueSlug($data['slug'], $id);

        // Xử lý upload Logo mới
        if ($request->hasFile('logo')) {
            // Xóa file cũ nếu có
            if ($brand->logo && !str_starts_with($brand->logo, 'http') && Storage::disk('public')->exists('clients/imgs/brands/' . $brand->logo)) {
                Storage::disk('public')->delete('clients/imgs/brands/' . $brand->logo);
            }

            $file = $request->file('logo');
            $filename = 'brand-' . $data['slug'] . '-' . time() . '.' . $file->getClientOriginalExtension();
            $file->storeAs('clients/imgs/brands', $filename, 'public');
            $data['logo'] = $filename;
        } elseif ($request->filled('logo_url')) {
            $data['logo'] = $request->input('logo_url');
        }

        $brand->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Cập nhật thương hiệu thành công!',
            'data'    => $brand,
        ]);
    }

    /**
     * Remove the specified Brand.
     */
    public function apiDestroy($id): JsonResponse
    {
        $brand = Brand::withCount(['products', 'series'])->findOrFail($id);

        if ($brand->products_count > 0) {
            return response()->json([
                'success' => false,
                'message' => "Không thể xóa thương hiệu này vì đang có {$brand->products_count} sản phẩm liên kết.",
            ], 422);
        }

        if ($brand->series_count > 0) {
            return response()->json([
                'success' => false,
                'message' => "Không thể xóa thương hiệu này vì đang có {$brand->series_count} dòng sản phẩm (series) liên kết.",
            ], 422);
        }

        if ($brand->logo && !str_starts_with($brand->logo, 'http') && Storage::disk('public')->exists('clients/imgs/brands/' . $brand->logo)) {
            Storage::disk('public')->delete('clients/imgs/brands/' . $brand->logo);
        }

        $brand->delete();

        return response()->json([
            'success' => true,
            'message' => 'Đã xóa thương hiệu thành công!',
        ]);
    }

    /**
     * Đảm bảo Slug không bị trùng lặp.
     */
    private function uniqueSlug(string $slug, ?int $exceptId = null): string
    {
        $original = $slug;
        $count = 1;

        while (Brand::where('slug', $slug)
            ->when($exceptId, fn($q) => $q->where('id', '!=', $exceptId))
            ->exists()) {
            $slug = "{$original}-{$count}";
            $count++;
        }

        return $slug;
    }
}
