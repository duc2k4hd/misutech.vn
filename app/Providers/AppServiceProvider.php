<?php

namespace App\Providers;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Cache;
use App\Models\Category;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Schema::defaultStringLength(191);
        \Illuminate\Pagination\Paginator::useBootstrapFive();

        // Tự động nhận diện HTTPS khi chạy trên Production hoặc qua SSL proxy/Cloudflare (tránh lỗi Mixed Content)
        if (request()->isSecure() || request()->header('X-Forwarded-Proto') === 'https' || (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') || config('app.env') === 'production') {
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }

        // Chia sẻ dữ liệu toàn cục cho View bằng View Composer
        // QUAN TRỌNG: KHÔNG dùng Cache::rememberForever ở đây vì:
        // 1. Eloquent Collection 200KB+ khi serialize/unserialize sẽ mất relationships
        // 2. static $sharedData đã handle per-request caching hiệu quả (chỉ query 1 lần/request)
        // 3. Các queries này rất nhanh (<5ms) với index đầy đủ
        View::composer('*', function ($view) {
            static $sharedData = null;
            if ($sharedData === null) {
                try {
                    // Query thẳng từ DB (không qua cache) — an toàn, nhanh, không bao giờ lỗi serialization
                    $allCategories = Schema::hasTable('categories')
                        ? Category::whereNull('parent_id')
                            ->with('children.children')
                            ->orderBy('id', 'asc')
                            ->get()
                        : collect();

                    $mainCategories = $allCategories->take(9);

                    $settingsArray = Schema::hasTable('settings')
                        ? \App\Models\Setting::all()->pluck('value', 'key')->toArray()
                        : [];
                    $settings = (object) $settingsArray;

                    // Xử lý định dạng Hotline
                    $rawHotline = preg_replace('/[^0-9]/', '', $settings->hotline ?? '0866555212');
                    if (strlen($rawHotline) == 10) {
                        $settings->hotline = preg_replace('/(\d{4})(\d{3})(\d{3})/', '$1.$2.$3', $rawHotline);
                    } elseif (strlen($rawHotline) == 11) {
                        $settings->hotline = preg_replace('/(\d{4})(\d{3})(\d{4})/', '$1.$2.$3', $rawHotline);
                    } else {
                        $settings->hotline = $settings->hotline ?? '0866.555.212';
                    }

                    $global_banners = Schema::hasTable('banners')
                        ? \App\Models\Banner::where('status', 'active')->get()
                        : collect();

                    $supportContacts = Schema::hasTable('support_contacts')
                        ? \App\Models\SupportContact::where('is_active', true)
                            ->where('show_in_popup', true)
                            ->orderBy('sort_order', 'asc')
                            ->get()
                        : collect();

                    $sharedData = compact('allCategories', 'mainCategories', 'settings', 'global_banners', 'supportContacts');
                } catch (\Throwable $e) {
                    $sharedData = [
                        'allCategories'   => collect(),
                        'mainCategories'  => collect(),
                        'settings'        => (object) [],
                        'global_banners'  => collect(),
                        'supportContacts' => collect(),
                    ];
                }
            }

            $view->with($sharedData);
        });


        // Tự động xóa cache khi có thay đổi dữ liệu trong Admin
        \App\Models\Product::saved(function ($product) {
            Cache::forget('home_flash_sale_product_ids');
            Cache::forget('home_featured_product_ids');
            Cache::forget('home_category_sections_map');
            Cache::forget('shop_sidebar_categories');
            Cache::forget('shop_filter_brands');
            Cache::forget('shop_max_price');
            Cache::forget('quote_popular_products');
            Cache::forget('document_filter_brands');
            Cache::forget('document_filter_categories');
            Cache::forget('document_total_count');
            if ($product->series_id) {
                Cache::forget("series_models_embedded_{$product->series_id}");
                Cache::forget("series_related_{$product->series_id}");
            }
            if ($product->category_id) {
                Cache::forget("category_related_product_ids_{$product->category_id}");
            }
        });
        \App\Models\Product::deleted(function ($product) {
            Cache::forget('home_flash_sale_product_ids');
            Cache::forget('home_featured_product_ids');
            Cache::forget('home_category_sections_map');
            Cache::forget('shop_sidebar_categories');
            Cache::forget('shop_filter_brands');
            Cache::forget('shop_max_price');
            Cache::forget('quote_popular_products');
            Cache::forget('document_filter_brands');
            Cache::forget('document_filter_categories');
            Cache::forget('document_total_count');
            if ($product->series_id) {
                Cache::forget("series_models_embedded_{$product->series_id}");
                Cache::forget("series_related_{$product->series_id}");
            }
            if ($product->category_id) {
                Cache::forget("category_related_product_ids_{$product->category_id}");
            }
        });

        \App\Models\Category::saved(function () {
            Cache::forget('home_category_sections_map');
            Cache::forget('home_blog_sections_map');
            Cache::forget('shop_sidebar_categories');
            Cache::forget('blog_sidebar_categories');
            Cache::forget('category_descendants_map_product');
            Cache::forget('category_descendants_map_post');
            Cache::forget('all_categories_hierarchy_product');
            Cache::forget('document_filter_categories');
        });
        \App\Models\Category::deleted(function () {
            Cache::forget('home_category_sections_map');
            Cache::forget('home_blog_sections_map');
            Cache::forget('shop_sidebar_categories');
            Cache::forget('blog_sidebar_categories');
            Cache::forget('category_descendants_map_product');
            Cache::forget('category_descendants_map_post');
            Cache::forget('all_categories_hierarchy_product');
            Cache::forget('document_filter_categories');
        });

        \App\Models\Series::saved(function ($series) {
            Cache::forget("series_models_embedded_{$series->id}");
            Cache::forget("series_related_{$series->id}");
        });
        \App\Models\Series::deleted(function ($series) {
            Cache::forget("series_models_embedded_{$series->id}");
            Cache::forget("series_related_{$series->id}");
        });

        \App\Models\Brand::saved(function () {
            Cache::forget('shop_filter_brands');
            Cache::forget('all_brands_page');
            Cache::forget('document_filter_brands');
        });
        \App\Models\Brand::deleted(function () {
            Cache::forget('shop_filter_brands');
            Cache::forget('all_brands_page');
            Cache::forget('document_filter_brands');
        });

        \App\Models\Media::saved(function () {
            Cache::forget('document_filter_brands');
            Cache::forget('document_filter_categories');
            Cache::forget('document_total_count');
        });
        \App\Models\Media::deleted(function () {
            Cache::forget('document_filter_brands');
            Cache::forget('document_filter_categories');
            Cache::forget('document_total_count');
        });

        \App\Models\Banner::saved(function () {
            Cache::forget('home_banners_grouped');
            Cache::forget('global_banners');
        });
        \App\Models\Banner::deleted(function () {
            Cache::forget('home_banners_grouped');
            Cache::forget('global_banners');
        });

        \App\Models\Post::saved(function () {
            Cache::forget('home_blog_sections_map');
            Cache::forget('blog_sidebar_categories');
            Cache::forget('blog_popular_post_ids');
            Cache::forget('blog_recent_post_ids');
        });
        \App\Models\Post::deleted(function () {
            Cache::forget('home_blog_sections_map');
            Cache::forget('blog_sidebar_categories');
            Cache::forget('blog_popular_post_ids');
            Cache::forget('blog_recent_post_ids');
        });

        \App\Models\Setting::saved(function () {
            Cache::forget('global_settings');
        });
        \App\Models\SupportContact::saved(function () {
            Cache::forget('global_support_contacts');
        });
        \App\Models\SupportContact::deleted(function () {
            Cache::forget('global_support_contacts');
        });
    }
}