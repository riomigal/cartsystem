<?php

use App\Http\Controllers\Api\V1\ApiNotFoundController;
use App\Http\Controllers\Api\V1\CartController;
use App\Http\Controllers\Api\V1\LoginController;
use Illuminate\Http\Request;
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

Route::middleware(['respond.json', 'log.request'])->group(function () {

    Route::post('/login', [LoginController::class, 'login'])->name('api.login');

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/cart/store', [CartController::class, 'store'])->name('api.cart.store');
        Route::get('/cart', [CartController::class, 'index'])->name('api.cart');
    });
});


/* Handles 404 Invalid routes (not found)
@hideFromAPIDocumentation
*/
Route::any('{any?}', [ApiNotFoundController::class, 'notFound'])->where('any', '.*');
