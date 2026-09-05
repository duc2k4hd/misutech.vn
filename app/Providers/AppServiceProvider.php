<?php

namespace App\Providers;

use App\Models\Banner;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Media;
use App\Models\Post;
use App\Models\Product;
use App\Models\Series;
use App\Models\Setting;
use App\Models\SupportContact;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

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
        Paginator::useBootstrapFive();

        // Tự động nhận diện HTTPS khi chạy trên Production hoặc qua SSL proxy/Cloudflare (tránh lỗi Mixed Content)
        if (!app()->runningInConsole()) {
            if (request()->isSecure() || request()->header('X-Forwarded-Proto') === 'https' || (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') || config('app.env') === 'production') {
                URL::forceScheme('https');
            }
        }

        // Chia sẻ dữ liệu toàn cục cho View bằng View Composer
        View::composer('*', function ($view) {
            static $sharedData = null;
            if ($sharedData === null) {
                try {
                    $allCategories = Category::whereNull('parent_id')
                        ->where('type', 'product')
                        ->where('status', 'active')
                        ->with('children.children')
                        ->orderBy('id', 'asc')
                        ->get();

                    $mainCategories = $allCategories->take(9);

                    $settingsArray = Setting::select(['key', 'value'])->pluck('value', 'key')->toArray();
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

                    $global_banners = Banner::select(['id', 'title', 'image', 'link', 'position', 'status'])
                        ->where('status', 'active')
                        ->get();

                    $supportContacts = SupportContact::where('is_active', true)
                        ->where('show_in_popup', true)
                        ->orderBy('sort_order', 'asc')
                        ->get();

                    $sharedData = compact('allCategories', 'mainCategories', 'settings', 'global_banners', 'supportContacts');
                } catch (\Throwable $e) {
                    $sharedData = [
                        'allCategories' => collect(),
                        'mainCategories' => collect(),
                        'settings' => (object) [],
                        'global_banners' => collect(),
                        'supportContacts' => collect(),
                    ];
                }
            }

            $view->with($sharedData);
        });

        // Tự động xóa cache khi có thay đổi dữ liệu trong Admin
        Product::saved(function ($product) {
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
        Product::deleted(function ($product) {
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

        Category::saved(function () {
            Cache::forget('home_category_sections_map');
            Cache::forget('home_blog_sections_map');
            Cache::forget('shop_sidebar_categories');
            Cache::forget('blog_sidebar_categories');
            Cache::forget('category_descendants_map_product');
            Cache::forget('category_descendants_map_post');
            Cache::forget('all_categories_hierarchy_product');
            Cache::forget('document_filter_categories');
        });
        Category::deleted(function () {
            Cache::forget('home_category_sections_map');
            Cache::forget('home_blog_sections_map');
            Cache::forget('shop_sidebar_categories');
            Cache::forget('blog_sidebar_categories');
            Cache::forget('category_descendants_map_product');
            Cache::forget('category_descendants_map_post');
            Cache::forget('all_categories_hierarchy_product');
            Cache::forget('document_filter_categories');
        });

        Series::saved(function ($series) {
            Cache::forget("series_models_embedded_{$series->id}");
            Cache::forget("series_related_{$series->id}");
        });
        Series::deleted(function ($series) {
            Cache::forget("series_models_embedded_{$series->id}");
            Cache::forget("series_related_{$series->id}");
        });

        Brand::saved(function () {
            Cache::forget('shop_filter_brands');
            Cache::forget('all_brands_page');
            Cache::forget('document_filter_brands');
        });
        Brand::deleted(function () {
            Cache::forget('shop_filter_brands');
            Cache::forget('all_brands_page');
            Cache::forget('document_filter_brands');
        });

        Media::saved(function () {
            Cache::forget('document_filter_brands');
            Cache::forget('document_filter_categories');
            Cache::forget('document_total_count');
        });
        Media::deleted(function () {
            Cache::forget('document_filter_brands');
            Cache::forget('document_filter_categories');
            Cache::forget('document_total_count');
        });

        Banner::saved(function () {
            Cache::forget('home_banners_grouped');
            Cache::forget('global_banners');
        });
        Banner::deleted(function () {
            Cache::forget('home_banners_grouped');
            Cache::forget('global_banners');
        });

        Post::saved(function () {
            Cache::forget('home_blog_sections_map');
            Cache::forget('blog_sidebar_categories');
            Cache::forget('blog_popular_post_ids');
            Cache::forget('blog_recent_post_ids');
        });
        Post::deleted(function () {
            Cache::forget('home_blog_sections_map');
            Cache::forget('blog_sidebar_categories');
            Cache::forget('blog_popular_post_ids');
            Cache::forget('blog_recent_post_ids');
        });

        Setting::saved(function () {
            Cache::forget('global_settings');
        });
        SupportContact::saved(function () {
            Cache::forget('global_support_contacts');
        });
        SupportContact::deleted(function () {
            Cache::forget('global_support_contacts');
        });
    }
}
