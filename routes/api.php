<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Clients\ShopController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::prefix('v1')->group(function () {
    Route::get('/products/load-more', [ShopController::class, 'loadMore'])
        ->middleware('throttle:120,1')
        ->name('api.products.load-more');
        
    // Adding web middleware here allows session access for mock cart functionality
    Route::post('/cart/add', [ShopController::class, 'addCart'])
        ->middleware('web')
        ->middleware('throttle:1,0.033333')
        ->name('api.cart.add');
        
    Route::post('/cart/update', [ShopController::class, 'updateCart'])
        ->middleware('web')
        ->name('api.cart.update');
        
    Route::post('/cart/clear', [ShopController::class, 'clearCart'])
        ->middleware('web')
        ->name('api.cart.clear');
});
