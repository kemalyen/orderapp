
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\OrderController;
use App\Http\Controllers\Api\V1\ProductController;

Route::middleware(['auth:sanctum'])->group(function () {
    Route::apiResource('orders', OrderController::class)->only(['index', 'store', 'show']);
    Route::apiResource('products', ProductController::class)->only(['index', 'show']);
});
