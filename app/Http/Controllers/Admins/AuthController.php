<?php

namespace App\Http\Controllers\Admins;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('admins.pages.auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);

        $remember = $request->has('remember');

        if (Auth::attempt($credentials, $remember)) {
            $user = Auth::user();
            if ($user->role === 'admin') {
                $request->session()->regenerate();
                return redirect()->intended(route('admin.dashboard'));
            } else {
                Auth::logout();
                return back()->with('error', 'Bạn không có quyền truy cập trang quản trị.');
            }
        }

        return back()->with('error', 'Email hoặc mật khẩu không chính xác.');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('admin.login')->with('success', 'Đã đăng xuất thành công.');
    }

    /**
     * Xóa cache ứng dụng (page caches).
     * Global data (categories, settings) được query thẳng từ DB mỗi request nên không cần warmup.
     */
    public function clearCache()
    {
        try {
            // 1. Xóa cache compiled files
            Artisan::call('view:clear');
            Artisan::call('route:clear');
            Artisan::call('config:clear');

            // 2. Xóa các cache dữ liệu trang theo từng key (KHÔNG flush toàn bộ)
            $appCacheKeys = [
                'home_flash_sale_product_ids',
                'home_featured_product_ids',
                'home_category_sections_map',
                'home_blog_sections_map',
                'home_banners_grouped',
                'shop_sidebar_categories',
                'shop_filter_brands',
                'shop_max_price',
                'quote_popular_products',
                'blog_sidebar_categories',
                'blog_popular_post_ids',
                'blog_recent_post_ids',
                'all_brands_page',
                'document_filter_brands',
                'document_filter_categories',
                'document_total_count',
                'category_descendants_map_product',
                'category_descendants_map_post',
                'all_categories_hierarchy_product',
                // Legacy keys (nếu còn tồn tại từ version cũ)
                'global_categories_tree',
                'global_settings',
                'global_banners',
                'global_support_contacts',
            ];
            foreach ($appCacheKeys as $key) {
                Cache::forget($key);
            }

            return response()->json([
                'success' => true,
                'message' => 'Đã làm mới cache hệ thống thành công!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi xóa cache: ' . $e->getMessage()
            ], 500);
        }
    }



    /**
     * Đổi mật khẩu tài khoản Admin.
     */
    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password'     => 'required|min:6|confirmed',
        ], [
            'current_password.required' => 'Vui lòng nhập mật khẩu hiện tại.',
            'new_password.required'     => 'Vui lòng nhập mật khẩu mới.',
            'new_password.min'          => 'Mật khẩu mới tối thiểu 6 ký tự.',
            'new_password.confirmed'    => 'Xác nhận mật khẩu mới không khớp.',
        ]);

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Mật khẩu hiện tại không chính xác.'
            ], 422);
        }

        $user->password = Hash::make($request->new_password);
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Đổi mật khẩu tài khoản thành công!'
        ]);
    }
}
