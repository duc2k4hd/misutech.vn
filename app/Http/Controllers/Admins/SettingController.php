<?php

namespace App\Http\Controllers\Admins;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use App\Models\Setting;

class SettingController extends Controller
{
    public function index()
    {
        return view('admins.pages.settings.index');
    }

    public function apiList()
    {
        $settings = Setting::latest()->get();
        return response()->json(['data' => $settings]);
    }

    public function apiStore(Request $request)
    {
        $request->validate([
            'key' => 'required|string|max:255',
            'type' => 'required|in:string,text,textarea,integer,float,number,boolean,json,email,url,image',
        ]);

        $value = $request->value;

        if ($request->type === 'image') {
            if ($request->hasFile('value')) {
                $file = $request->file('value');
                $filename = \Illuminate\Support\Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . $file->getClientOriginalExtension();
                $destinationPath = public_path('storage/clients/imgs/settings');
                $file->move($destinationPath, $filename);
                $value = $filename;
            } elseif ($request->id) {
                $setting = Setting::find($request->id);
                $value = $setting ? $setting->value : '';
            } else {
                $request->validate(['value' => 'required|image']);
            }
        } elseif ($request->type === 'json') {
            $request->validate(['value' => 'required|string']);
            json_decode($value);
            if (json_last_error() !== JSON_ERROR_NONE) {
                return response()->json(['errors' => ['value' => ['Dữ liệu JSON không hợp lệ! Vui lòng kiểm tra lại cú pháp.']]], 422);
            }
        } else {
            $request->validate(['value' => 'required']);
        }

        $setting = Setting::updateOrCreate(
            ['id' => $request->id],
            [
                'key' => $request->key,
                'value' => $value,
                'type' => $request->type,
            ]
        );

        Cache::forget('global_settings');

        return response()->json([
            'success' => true,
            'message' => 'Lưu cài đặt thành công!',
            'data' => $setting
        ]);
    }

    public function apiShow($id)
    {
        $setting = Setting::findOrFail($id);
        return response()->json(['success' => true, 'data' => $setting]);
    }

    public function apiDestroy($id)
    {
        Setting::destroy($id);
        Cache::forget('global_settings');
        return response()->json([
            'success' => true,
            'message' => 'Xóa cài đặt thành công!'
        ]);
    }
}
