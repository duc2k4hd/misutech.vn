<?php

namespace App\Http\Controllers\Admins;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use App\Models\Banner;
use Illuminate\Support\Facades\File;

class BannerController extends Controller
{
    public function index()
    {
        return view('admins.pages.banners.index');
    }

    public function apiList()
    {
        try {
            $banners = Banner::latest()->get();
            return response()->json(['data' => array_values($banners->toArray())]);
        } catch (\Throwable $e) {
            \Log::error('Banner apiList error: ' . $e->getMessage());
            return response()->json(['data' => [], 'error' => $e->getMessage()]);
        }
    }

    public function apiStore(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'link' => 'nullable|string|max:255',
            'position' => 'nullable|integer',
            'status' => 'required|in:active,draft',
        ]);

        $image = null;

        if ($request->hasFile('image')) {
            $request->validate(['image' => 'image|mimes:jpeg,png,jpg,gif,webp|max:5120']);
            $file = $request->file('image');
            $filename = \Illuminate\Support\Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . $file->getClientOriginalExtension();
            $destinationPath = public_path('storage/clients/imgs/banners');
            $file->move($destinationPath, $filename);
            $image = $filename;
        } elseif ($request->id) {
            $banner = Banner::find($request->id);
            $image = $banner ? $banner->image : null;
        } else {
            $request->validate(['image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:5120']);
        }

        $banner = Banner::updateOrCreate(
            ['id' => $request->id],
            [
                'title' => $request->title,
                'image' => $image,
                'link' => $request->link,
                'position' => $request->position,
                'status' => $request->status,
            ]
        );

        Cache::forget('global_banners');

        return response()->json([
            'success' => true,
            'message' => 'Lưu banner thành công!',
            'data' => $banner
        ]);
    }

    public function apiShow($id)
    {
        $banner = Banner::findOrFail($id);
        return response()->json(['success' => true, 'data' => $banner]);
    }

    public function apiDestroy($id)
    {
        $banner = Banner::findOrFail($id);
        
        // Xóa ảnh cũ
        if ($banner->image) {
            $oldImagePath = public_path('storage/clients/imgs/banners/' . $banner->image);
            if (File::exists($oldImagePath)) {
                File::delete($oldImagePath);
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
