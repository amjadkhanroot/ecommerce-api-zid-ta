<?php

use App\Http\Controllers\API\App\CartController;
use App\Http\Controllers\API\App\ProductController;
use App\Http\Controllers\API\Auth\AuthenticationController;

use App\Http\Controllers\API\Store\StoreSettingController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::namespace("API")->prefix('/v1')->group(function (){;

    //Public
    Route::namespace("Auth")->prefix('auth')->group(function (){
        Route::post('register', [AuthenticationController::class, 'register']);
        Route::post('login', [AuthenticationController::class, 'login']);
    });

    //Seller
    Route::namespace("App")->middleware(['auth:sanctum', 'check.seller'])->prefix('seller')->group(function (){
        Route::prefix('products')->group(function (){
            Route::get('/', [ProductController::class, 'list']);
            Route::post('create', [ProductController::class, 'create']);
        });

        Route::prefix('store')->group(function (){
            Route::post('setting', [StoreSettingController::class, 'setStoreSetting']);
        });
    });

    //Customer
    Route::namespace("App")->group(function (){
        Route::prefix('products')->group(function (){
            Route::get('/', [ProductController::class, 'list']);
        });

        Route::prefix('carts')->middleware('auth:sanctum')->group(function (){
            Route::get('/', [CartController::class, 'getMyCart']);
            Route::post('add', [CartController::class, 'addToCart']);
            Route::post('remove', [CartController::class, 'removeFromCart']);
        });
    });

});
