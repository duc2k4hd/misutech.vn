<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admins\AuthController;
use App\Http\Controllers\Admins\DashboardController;

Route::get('/', function () {
    return redirect()->route('admin.dashboard');
});

Route::middleware('guest')->group(function () {
    Route::get('login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('login', [AuthController::class, 'login'])->name('login.post');
});

Route::middleware('auth')->group(function () {
    Route::post('logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('logout', [AuthController::class, 'logout'])->name('logout.get');
    Route::post('api/clear-cache', [AuthController::class, 'clearCache'])->name('clear_cache');
    Route::post('api/change-password', [AuthController::class, 'changePassword'])->name('change_password');
    Route::get('api/global-search', [DashboardController::class, 'globalSearch'])->name('global_search');
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Settings (Cấu hình hệ thống)
    Route::get('settings', [\App\Http\Controllers\Admins\SettingController::class, 'index'])->name('settings.index');
    Route::match(['get', 'post'], 'api/settings-list', [\App\Http\Controllers\Admins\SettingController::class, 'apiList'])->name('api.settings.list');
    Route::get('api/settings', [\App\Http\Controllers\Admins\SettingController::class, 'apiList']);
    Route::post('api/settings-save-all', [\App\Http\Controllers\Admins\SettingController::class, 'apiSaveAll'])->name('api.settings.save_all');
    Route::post('api/settings', [\App\Http\Controllers\Admins\SettingController::class, 'apiStore'])->name('api.settings.store');
    Route::get('api/settings/{id}', [\App\Http\Controllers\Admins\SettingController::class, 'apiShow'])->name('api.settings.show');
    Route::delete('api/settings/{id}', [\App\Http\Controllers\Admins\SettingController::class, 'apiDestroy'])->name('api.settings.destroy');

    // Sitemap Management & Auto Generator
    Route::get('sitemaps', [\App\Http\Controllers\Admins\SitemapController::class, 'index'])->name('sitemaps.index');
    Route::get('api/sitemaps', [\App\Http\Controllers\Admins\SitemapController::class, 'apiGetInfo'])->name('api.sitemaps.info');
    Route::post('api/sitemaps/settings', [\App\Http\Controllers\Admins\SitemapController::class, 'apiSaveSettings'])->name('api.sitemaps.settings');
    Route::post('api/sitemaps/generate', [\App\Http\Controllers\Admins\SitemapController::class, 'apiGenerate'])->name('api.sitemaps.generate');

    // Banners
    Route::get('banners', [\App\Http\Controllers\Admins\BannerController::class, 'index'])->name('banners.index');
    Route::match(['get', 'post'], 'api/banners-list', [\App\Http\Controllers\Admins\BannerController::class, 'apiList'])->name('api.banners.list');
    Route::get('api/banners', [\App\Http\Controllers\Admins\BannerController::class, 'apiList']);
    Route::post('api/banners', [\App\Http\Controllers\Admins\BannerController::class, 'apiStore'])->name('api.banners.store');
    Route::get('api/banners/{id}', [\App\Http\Controllers\Admins\BannerController::class, 'apiShow'])->name('api.banners.show');
    Route::delete('api/banners/{id}', [\App\Http\Controllers\Admins\BannerController::class, 'apiDestroy'])->name('api.banners.destroy');
    Route::post('api/banners/toggle-status/{id}', [\App\Http\Controllers\Admins\BannerController::class, 'apiToggleStatus'])->name('api.banners.toggle');
    
    // Support & Hotline Contacts (Sale, Warranty, Custom Departments)
    Route::get('support-contacts', [\App\Http\Controllers\Admins\SupportContactController::class, 'index'])->name('support_contacts.index');
    Route::match(['get', 'post'], 'api/support-contacts-list', [\App\Http\Controllers\Admins\SupportContactController::class, 'apiList'])->name('api.support_contacts.list');
    Route::get('api/support-contacts', [\App\Http\Controllers\Admins\SupportContactController::class, 'apiList']);
    Route::post('api/support-contacts', [\App\Http\Controllers\Admins\SupportContactController::class, 'apiStore'])->name('api.support_contacts.store');
    Route::get('api/support-contacts/{id}', [\App\Http\Controllers\Admins\SupportContactController::class, 'apiShow'])->name('api.support_contacts.show');
    Route::delete('api/support-contacts/{id}', [\App\Http\Controllers\Admins\SupportContactController::class, 'apiDestroy'])->name('api.support_contacts.destroy');
    Route::post('api/support-contacts/toggle-status/{id}', [\App\Http\Controllers\Admins\SupportContactController::class, 'apiToggleStatus'])->name('api.support_contacts.toggle');

    // Quotes Management (Báo giá dự án & sản phẩm)
    Route::get('quotes', [\App\Http\Controllers\Admins\QuoteController::class, 'index'])->name('quotes.index');
    Route::match(['get', 'post'], 'api/quotes', [\App\Http\Controllers\Admins\QuoteController::class, 'apiList'])->name('api.quotes.list');
    Route::get('api/quotes/{id}', [\App\Http\Controllers\Admins\QuoteController::class, 'apiShow'])->name('api.quotes.show');
    Route::put('api/quotes/{id}/status', [\App\Http\Controllers\Admins\QuoteController::class, 'apiUpdateStatus'])->name('api.quotes.status');
    Route::delete('api/quotes/{id}', [\App\Http\Controllers\Admins\QuoteController::class, 'apiDestroy'])->name('api.quotes.destroy');
    Route::post('api/quotes-bulk-delete', [\App\Http\Controllers\Admins\QuoteController::class, 'apiBulkDestroy'])->name('api.quotes.bulk.destroy');

    // Contacts Management (Yêu cầu liên hệ & Tư vấn)
    Route::get('contacts', [\App\Http\Controllers\Admins\ContactController::class, 'index'])->name('contacts.index');
    Route::match(['get', 'post'], 'api/contacts', [\App\Http\Controllers\Admins\ContactController::class, 'apiList'])->name('api.contacts.list');
    Route::get('api/contacts/{id}', [\App\Http\Controllers\Admins\ContactController::class, 'apiShow'])->name('api.contacts.show');
    Route::put('api/contacts/{id}/status', [\App\Http\Controllers\Admins\ContactController::class, 'apiUpdateStatus'])->name('api.contacts.status');
    Route::delete('api/contacts/{id}', [\App\Http\Controllers\Admins\ContactController::class, 'apiDestroy'])->name('api.contacts.destroy');
    Route::post('api/contacts-bulk-delete', [\App\Http\Controllers\Admins\ContactController::class, 'apiBulkDestroy'])->name('api.contacts.bulk.destroy');

    // Reviews Management (Kiểm duyệt đánh giá sản phẩm)
    Route::get('reviews', [\App\Http\Controllers\Admins\ReviewController::class, 'index'])->name('reviews.index');
    Route::get('api/reviews', [\App\Http\Controllers\Admins\ReviewController::class, 'apiList'])->name('api.reviews.list');
    Route::put('api/reviews/{id}/status', [\App\Http\Controllers\Admins\ReviewController::class, 'apiUpdateStatus'])->name('api.reviews.status');
    Route::delete('api/reviews/{id}', [\App\Http\Controllers\Admins\ReviewController::class, 'apiDestroy'])->name('api.reviews.destroy');
    
    // Products
    Route::get('products', [\App\Http\Controllers\Admins\ProductController::class, 'index'])->name('products.index');
    Route::match(['get', 'post'], 'api/products-list', [\App\Http\Controllers\Admins\ProductController::class, 'apiList'])->name('api.products.list');
    Route::get('api/products', [\App\Http\Controllers\Admins\ProductController::class, 'apiList']);
    Route::post('api/products', [\App\Http\Controllers\Admins\ProductController::class, 'apiStore'])->name('api.products.store');
    Route::post('api/products/store', [\App\Http\Controllers\Admins\ProductController::class, 'apiStore']);
    Route::get('api/products/{id}', [\App\Http\Controllers\Admins\ProductController::class, 'apiShow'])->name('api.products.show');
    Route::put('api/products/{id}', [\App\Http\Controllers\Admins\ProductController::class, 'apiUpdate'])->name('api.products.update');
    Route::delete('api/products/{id}', [\App\Http\Controllers\Admins\ProductController::class, 'apiDestroy'])->name('api.products.destroy');
    Route::post('api/products-bulk-delete', [\App\Http\Controllers\Admins\ProductController::class, 'apiBulkDestroy'])->name('api.products.bulk.destroy');
    Route::post('api/products/{id}/restore', [\App\Http\Controllers\Admins\ProductController::class, 'apiRestore'])->name('api.products.restore');
    Route::delete('api/products/{id}/force-delete', [\App\Http\Controllers\Admins\ProductController::class, 'apiForceDelete'])->name('api.products.force.destroy');
    Route::post('api/products-bulk-restore', [\App\Http\Controllers\Admins\ProductController::class, 'apiBulkRestore'])->name('api.products.bulk.restore');
    Route::post('api/products-bulk-force-delete', [\App\Http\Controllers\Admins\ProductController::class, 'apiBulkForceDelete'])->name('api.products.bulk.force.destroy');
    
    // Import / Export Products
    Route::get('api/products-export', [\App\Http\Controllers\Admins\ProductController::class, 'apiExport'])->name('api.products.export');
    Route::post('api/products-import-batch', [\App\Http\Controllers\Admins\ProductController::class, 'apiImportBatch'])->name('api.products.import.batch');

    // Brands (Thương hiệu / Hãng sản xuất)
    Route::get('brands', [\App\Http\Controllers\Admins\BrandController::class, 'index'])->name('brands.index');
    Route::match(['get', 'post'], 'api/brands-list', [\App\Http\Controllers\Admins\BrandController::class, 'apiList'])->name('api.brands.list');
    Route::get('api/brands', [\App\Http\Controllers\Admins\BrandController::class, 'apiList']);
    Route::post('api/brands', [\App\Http\Controllers\Admins\BrandController::class, 'apiStore'])->name('api.brands.store');
    Route::get('api/brands/{id}', [\App\Http\Controllers\Admins\BrandController::class, 'apiShow'])->name('api.brands.show');
    Route::put('api/brands/{id}', [\App\Http\Controllers\Admins\BrandController::class, 'apiUpdate'])->name('api.brands.update');
    Route::delete('api/brands/{id}', [\App\Http\Controllers\Admins\BrandController::class, 'apiDestroy'])->name('api.brands.destroy');

    // Series (Dòng sản phẩm)
    Route::get('series', [\App\Http\Controllers\Admins\SeriesController::class, 'index'])->name('series.index');
    Route::match(['get', 'post'], 'api/series-list', [\App\Http\Controllers\Admins\SeriesController::class, 'apiList'])->name('api.series.list');
    Route::match(['get', 'post'], 'api/series', [\App\Http\Controllers\Admins\SeriesController::class, 'apiList']);
    Route::post('api/series', [\App\Http\Controllers\Admins\SeriesController::class, 'apiStore'])->name('api.series.store');
    Route::get('api/series/{id}', [\App\Http\Controllers\Admins\SeriesController::class, 'apiShow'])->name('api.series.show');
    Route::put('api/series/{id}', [\App\Http\Controllers\Admins\SeriesController::class, 'apiUpdate'])->name('api.series.update');
    Route::delete('api/series/{id}', [\App\Http\Controllers\Admins\SeriesController::class, 'apiDestroy'])->name('api.series.destroy');
    
    // Categories
    Route::get('categories', [\App\Http\Controllers\Admins\CategoryController::class, 'index'])->name('categories.index');
    Route::get('api/categories', [\App\Http\Controllers\Admins\CategoryController::class, 'apiList'])->name('categories.api.list');
    Route::post('api/categories', [\App\Http\Controllers\Admins\CategoryController::class, 'apiStore'])->name('categories.api.store');
    Route::get('api/categories/{id}', [\App\Http\Controllers\Admins\CategoryController::class, 'apiShow'])->name('categories.api.show');
    Route::delete('api/categories/{id}', [\App\Http\Controllers\Admins\CategoryController::class, 'apiDestroy'])->name('categories.api.destroy');
    Route::get('api/categories-export', [\App\Http\Controllers\Admins\CategoryController::class, 'apiExport'])->name('api.categories.export');
    Route::post('api/categories-import-batch', [\App\Http\Controllers\Admins\CategoryController::class, 'apiImportBatch'])->name('api.categories.import.batch');

    // Posts
    Route::get('posts', [\App\Http\Controllers\Admins\PostController::class, 'index'])->name('posts.index');
    Route::get('api/posts', [\App\Http\Controllers\Admins\PostController::class, 'apiList'])->name('api.posts.list');
    Route::post('api/posts', [\App\Http\Controllers\Admins\PostController::class, 'apiStore'])->name('api.posts.store');
    Route::get('api/posts/{id}', [\App\Http\Controllers\Admins\PostController::class, 'apiShow'])->name('api.posts.show');
    Route::put('api/posts/{id}', [\App\Http\Controllers\Admins\PostController::class, 'apiUpdate'])->name('api.posts.update');
    Route::delete('api/posts/{id}', [\App\Http\Controllers\Admins\PostController::class, 'apiDestroy'])->name('api.posts.destroy');
    Route::post('api/posts-bulk-delete', [\App\Http\Controllers\Admins\PostController::class, 'apiBulkDestroy'])->name('api.posts.bulk.destroy');
    Route::post('api/posts/{id}/restore', [\App\Http\Controllers\Admins\PostController::class, 'apiRestore'])->name('api.posts.restore');
    Route::delete('api/posts/{id}/force-delete', [\App\Http\Controllers\Admins\PostController::class, 'apiForceDelete'])->name('api.posts.force.destroy');
    Route::post('api/posts-bulk-restore', [\App\Http\Controllers\Admins\PostController::class, 'apiBulkRestore'])->name('api.posts.bulk.restore');
    Route::post('api/posts-bulk-force-delete', [\App\Http\Controllers\Admins\PostController::class, 'apiBulkForceDelete'])->name('api.posts.bulk.force.destroy');
    Route::get('api/posts-export', [\App\Http\Controllers\Admins\PostController::class, 'apiExport'])->name('api.posts.export');
    Route::post('api/posts-import-batch', [\App\Http\Controllers\Admins\PostController::class, 'apiImportBatch'])->name('api.posts.import.batch');

    // Media Manager
    Route::get('media', [\App\Http\Controllers\Admins\MediaController::class, 'index'])->name('media.index');

    // Browse & tree
    Route::get('api/media',          [\App\Http\Controllers\Admins\MediaController::class, 'apiBrowse'])->name('api.media.browse');
    Route::get('api/media/tree',     [\App\Http\Controllers\Admins\MediaController::class, 'apiFolderTree'])->name('api.media.tree');

    // Upload
    Route::post('api/media/upload',  [\App\Http\Controllers\Admins\MediaController::class, 'apiUpload'])->name('api.media.upload');

    // File operations
    Route::put('api/media/{id}',            [\App\Http\Controllers\Admins\MediaController::class, 'apiUpdate'])->name('api.media.update');
    Route::post('api/media/{id}/rename',    [\App\Http\Controllers\Admins\MediaController::class, 'apiRename'])->name('api.media.rename');
    Route::post('api/media/{id}/move',      [\App\Http\Controllers\Admins\MediaController::class, 'apiMove'])->name('api.media.move');
    Route::post('api/media/delete',         [\App\Http\Controllers\Admins\MediaController::class, 'apiDelete'])->name('api.media.delete');

    // Folder operations
    Route::post('api/media/folders',        [\App\Http\Controllers\Admins\MediaController::class, 'apiFolderCreate'])->name('api.media.folder.create');
    Route::put('api/media/folders',         [\App\Http\Controllers\Admins\MediaController::class, 'apiFolderRename'])->name('api.media.folder.rename');
    Route::delete('api/media/folders',      [\App\Http\Controllers\Admins\MediaController::class, 'apiFolderDelete'])->name('api.media.folder.delete');

    // Sync
    Route::post('api/media/sync',           [\App\Http\Controllers\Admins\MediaController::class, 'apiSync'])->name('api.media.sync');
});
