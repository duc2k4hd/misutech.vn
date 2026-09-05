<?php

namespace App\Http\Controllers\Admins;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use App\Models\Banner;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class BannerController extends Controller
{
    public function index()
    {
        return view('admins.pages.banners.index');
    }

    public function apiList(Request $request)
    {
        try {
            $query = Banner::query();

            // Lọc theo từ khóa (tiêu đề, link, ID)
            if ($request->filled('keyword')) {
                $kw = trim($request->keyword);
                $query->where(function ($q) use ($kw) {
                    $q->where('title', 'like', "%{$kw}%")
                      ->orWhere('link', 'like', "%{$kw}%")
                      ->orWhere('id', $kw);
                });
            }

            // Lọc theo vị trí (position)
            if ($request->filled('position') && $request->position !== 'all') {
                $pos = $request->position;
                if ($pos === 'slider') {
                    $query->where('position', 1);
                } elseif ($pos === 'secondary') {
                    $query->whereIn('position', [2, 3]);
                } elseif ($pos === 'other') {
                    $query->where(function ($q) {
                        $q->whereNull('position')->orWhere('position', '>', 3)->orWhere('position', '<', 1);
                    });
                } elseif (is_numeric($pos)) {
                    $query->where('position', (int)$pos);
                }
            }

            // Lọc theo trạng thái
            if ($request->filled('status') && $request->status !== 'all') {
                $query->where('status', $request->status);
            }

            // Sắp xếp
            $sort = $request->get('sort', 'position_asc');
            switch ($sort) {
                case 'position_asc':
                    $query->orderByRaw('COALESCE(position, 9999) ASC')->orderBy('id', 'desc');
                    break;
                case 'position_desc':
                    $query->orderByRaw('COALESCE(position, 0) DESC')->orderBy('id', 'desc');
                    break;
                case 'latest':
                    $query->latest('id');
                    break;
                case 'oldest':
                    $query->oldest('id');
                    break;
                case 'title_asc':
                    $query->orderBy('title', 'asc');
                    break;
                case 'title_desc':
                    $query->orderBy('title', 'desc');
                    break;
                default:
                    $query->orderByRaw('COALESCE(position, 9999) ASC')->orderBy('id', 'desc');
                    break;
            }

            $banners = $query->get();

            // Tính toán số liệu thống kê toàn cục
            $allBanners = Banner::select(['id', 'position', 'status'])->get();
            $stats = [
                'total' => $allBanners->count(),
                'slider' => $allBanners->where('position', 1)->count(),
                'secondary' => $allBanners->filter(function ($b) {
                    return in_array($b->position, [2, 3]);
                })->count(),
                'other' => $allBanners->filter(function ($b) {
                    return !in_array($b->position, [1, 2, 3]);
                })->count(),
                'active' => $allBanners->where('status', 'active')->count(),
                'draft' => $allBanners->where('status', 'draft')->count(),
            ];

            return response()->json([
                'success' => true,
                'data' => array_values($banners->toArray()),
                'stats' => $stats,
                'total' => $banners->count()
            ]);
        } catch (\Throwable $e) {
            \Log::error('Banner apiList error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'data' => [],
                'stats' => ['total' => 0, 'slider' => 0, 'secondary' => 0, 'other' => 0, 'active' => 0, 'draft' => 0],
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function apiStore(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'link' => 'nullable|string|max:500',
            'position' => 'nullable|integer|min:0',
            'status' => 'required|in:active,draft',
        ], [
            'title.required' => 'Vui lòng nhập tiêu đề banner',
            'status.required' => 'Vui lòng chọn trạng thái banner',
        ]);

        $image = null;

        if ($request->hasFile('image')) {
            $request->validate([
                'image' => 'image|mimes:jpeg,png,jpg,gif,webp,svg|max:5120'
            ], [
                'image.image' => 'File tải lên phải là hình ảnh hợp lệ',
                'image.mimes' => 'Hình ảnh phải có định dạng jpeg, png, jpg, gif, webp hoặc svg',
                'image.max' => 'Kích thước ảnh tối đa là 5MB',
            ]);

            $file = $request->file('image');
            $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $filename = Str::slug($originalName) . '-' . time() . '.' . $file->getClientOriginalExtension();
            $destinationPath = public_path('storage/clients/imgs/banners');

            if (!File::isDirectory($destinationPath)) {
                File::makeDirectory($destinationPath, 0755, true, true);
            }

            $file->move($destinationPath, $filename);
            $image = $filename;

            // Xóa ảnh cũ nếu đang cập nhật
            if ($request->id) {
                $oldBanner = Banner::find($request->id);
                if ($oldBanner && $oldBanner->image) {
                    $oldImagePath = public_path('storage/clients/imgs/banners/' . $oldBanner->image);
                    if (File::exists($oldImagePath)) {
                        @File::delete($oldImagePath);
                    }
                }
            }
        } elseif ($request->id) {
            $banner = Banner::find($request->id);
            $image = $banner ? $banner->image : null;
        } else {
            $request->validate([
                'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp,svg|max:5120'
            ], [
                'image.required' => 'Vui lòng chọn hình ảnh cho banner mới',
            ]);
        }

        $banner = Banner::updateOrCreate(
            ['id' => $request->id],
            [
                'title' => trim($request->title),
                'image' => $image,
                'link' => $request->link ? trim($request->link) : null,
                'position' => $request->filled('position') ? (int)$request->position : 1,
                'status' => $request->status,
            ]
        );

        Cache::forget('global_banners');

        return response()->json([
            'success' => true,
            'message' => $request->id ? 'Cập nhật banner thành công!' : 'Tạo mới banner thành công!',
            'data' => $banner
        ]);
    }

    public function apiShow($id)
    {
        $banner = Banner::findOrFail($id);
        return response()->json(['success' => true, 'data' => $banner]);
    }

    public function apiToggleStatus($id)
    {
        try {
            $banner = Banner::findOrFail($id);
            $banner->status = $banner->status === 'active' ? 'draft' : 'active';
            $banner->save();

            Cache::forget('global_banners');

            return response()->json([
                'success' => true,
                'message' => $banner->status === 'active' ? 'Đã bật hiển thị banner!' : 'Đã chuyển banner sang tạm ẩn!',
                'data' => [
                    'id' => $banner->id,
                    'status' => $banner->status
                ]
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi cập nhật trạng thái: ' . $e->getMessage()
            ], 500);
        }
    }

    public function apiDestroy($id)
    {
        $banner = Banner::findOrFail($id);
        
        // Xóa ảnh cũ
        if ($banner->image) {
            $oldImagePath = public_path('storage/clients/imgs/banners/' . $banner->image);
            if (File::exists($oldImagePath)) {
                @File::delete($oldImagePath);
            }
        }

        $banner->delete();
        Cache::forget('global_banners');
        
        return response()->json([
            'success' => true,
            'message' => 'Xóa banner thành công!'
        ]);
    }
}
