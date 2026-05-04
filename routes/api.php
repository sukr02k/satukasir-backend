<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/setup', [App\Http\Controllers\Api\AuthController::class, 'initialSetup']);

Route::middleware('throttle:5,1')->post('/login', [App\Http\Controllers\Api\AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [App\Http\Controllers\Api\AuthController::class, 'logout']);
    Route::get('/me', [App\Http\Controllers\Api\AuthController::class, 'me']);
    Route::post('/refresh', [App\Http\Controllers\Api\AuthController::class, 'refresh']);
    Route::get('/my-outlet', [App\Http\Controllers\Api\AuthController::class, 'getOutletByUser']);

    Route::prefix('cashiers')->group(function () {
        Route::get('/', [App\Http\Controllers\Api\AuthController::class, 'getCashiers']);
        Route::post('/', [App\Http\Controllers\Api\AuthController::class, 'addCashier']);
        Route::put('/{id}', [App\Http\Controllers\Api\AuthController::class, 'updateCashier']);
        Route::delete('/{id}', [App\Http\Controllers\Api\AuthController::class, 'deleteCashier']);
    });

    Route::get('/outlet', [App\Http\Controllers\Api\OutletController::class, 'getOutlet']);
    Route::put('/outlet', [App\Http\Controllers\Api\OutletController::class, 'updateOutlet']);

    Route::prefix('categories')->group(function () {
        Route::get('/', [App\Http\Controllers\Api\CategoryController::class, 'getCategories']);
        Route::post('/', [App\Http\Controllers\Api\CategoryController::class, 'addCategory']);
        Route::put('/{id}', [App\Http\Controllers\Api\CategoryController::class, 'updateCategory']);
        Route::delete('/{id}', [App\Http\Controllers\Api\CategoryController::class, 'deleteCategory']);
    });

    Route::prefix('products')->group(function () {
        Route::get('/', [App\Http\Controllers\Api\ProductController::class, 'getProducts']);
        Route::get('/{id}', [App\Http\Controllers\Api\ProductController::class, 'getProduct']);
        Route::post('/', [App\Http\Controllers\Api\ProductController::class, 'addProduct']);
        Route::put('/{id}', [App\Http\Controllers\Api\ProductController::class, 'updateProduct']);
        Route::post('/{id}/with-image', [App\Http\Controllers\Api\ProductController::class, 'updateProductWithImage']);
        Route::delete('/{id}', [App\Http\Controllers\Api\ProductController::class, 'deleteProduct']);
    });

    Route::prefix('orders')->group(function () {
        Route::post('/', [App\Http\Controllers\Api\OrderController::class, 'addOrder']);
        Route::get('/', [App\Http\Controllers\Api\OrderController::class, 'getOrders']);
        Route::get('/{id}', [App\Http\Controllers\Api\OrderController::class, 'getOrder']);
        Route::delete('/{id}', [App\Http\Controllers\Api\OrderController::class, 'deleteOrder']);
    });

    Route::prefix('printer')->group(function () {
        Route::get('/', [App\Http\Controllers\Api\PrinterController::class, 'getPrinter']);
        Route::post('/', [App\Http\Controllers\Api\PrinterController::class, 'addPrinter']);
        Route::put('/{id}', [App\Http\Controllers\Api\PrinterController::class, 'updatePrinter']);
        Route::delete('/{id}', [App\Http\Controllers\Api\PrinterController::class, 'deletePrinter']);
    });

    Route::prefix('settings')->group(function () {
        Route::get('/', [App\Http\Controllers\Api\BusinessSettingController::class, 'getBusinessSettings']);
        Route::post('/', [App\Http\Controllers\Api\BusinessSettingController::class, 'addBusinessSetting']);
        Route::put('/{id}', [App\Http\Controllers\Api\BusinessSettingController::class, 'updateBusinessSetting']);
        Route::delete('/{id}', [App\Http\Controllers\Api\BusinessSettingController::class, 'deleteBusinessSetting']);
    });

    Route::prefix('sales-report')->group(function () {
        Route::post('/daily', [App\Http\Controllers\Api\SalesReportController::class, 'getDailySalesReport']);
        Route::post('/monthly', [App\Http\Controllers\Api\SalesReportController::class, 'getMonthlySalesReport']);
        Route::get('/summary', [App\Http\Controllers\Api\SalesReportController::class, 'getSalesSummary']);
    });
});