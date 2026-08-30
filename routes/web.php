<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Clients\HomeController;
use App\Http\Controllers\Clients\CategoryController;

use App\Http\Controllers\Clients\ShopController;
use App\Http\Controllers\Clients\ProductController;
use App\Http\Controllers\Clients\SeriesController;
use App\Http\Controllers\Clients\DocumentController;
use App\Http\Controllers\Clients\BlogController;
use App\Http\Controllers\Clients\ContactController;
use App\Http\Controllers\Clients\QuoteController;

Route::get('/', [HomeController::class, 'index'])->name('home.index');

Route::get('/cua-hang', [ShopController::class, 'index'])->name('shop.index');
Route::get('/thuong-hieu', [ShopController::class, 'brands'])->name('brands.index');
Route::get('/danh-muc/{slug}', [CategoryController::class, 'show'])->name('categories.show');
Route::get('/series/{slug}', [SeriesController::class, 'show'])->name('series.show');
Route::get('/san-pham/{slug}', [ProductController::class, 'show'])->name('product.show');
Route::post('/san-pham/{slug}/danh-gia', [ProductController::class, 'storeReview'])->name('product.review.store');
Route::get('/gio-hang', [ShopController::class, 'cart'])->name('cart.index');
Route::get('/documents', [DocumentController::class, 'index'])->name('documents.index');
Route::get('/tai-lieu', [DocumentController::class, 'index'])->name('documents.alias');
Route::get('/documents/{id}/download', [DocumentController::class, 'download'])->name('documents.download');
Route::get('/tai-lieu/{id}/download', [DocumentController::class, 'download'])->name('documents.download.alias');
Route::get('/blogs', [BlogController::class, 'index'])->name('blogs.index');
Route::get('/blog/{slug}', [BlogController::class, 'show'])->name('blogs.show');
Route::get('/tin-tuc', [BlogController::class, 'index'])->name('blogs.news.alias');
Route::get('/tin-tuc/{slug}', [BlogController::class, 'show'])->name('blogs.show.alias');

// Contact routes
Route::get('/lien-he', [ContactController::class, 'index'])->name('contact.index');
Route::post('/lien-he', [ContactController::class, 'submit'])->name('contact.submit');

// Online Instant Quote & PDF Generation tool
Route::get('/bao-gia', [QuoteController::class, 'index'])->name('quote.index');
Route::get('/api/quote/search-products', [QuoteController::class, 'searchProducts'])->name('quote.api.search');
Route::post('/quote/track', [QuoteController::class, 'saveAndTrack'])->name('quote.track');
