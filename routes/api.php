
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\OrderController;


Route::middleware(['auth:sanctum'])->group(function () {
    Route::apiResource('orders', OrderController::class);
});
