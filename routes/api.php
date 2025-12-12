<?php

use App\Http\Controllers\Authentication\Login\LoginController;
use App\Http\Controllers\Authentication\Logout\LogoutController;
use App\Http\Controllers\Order\CancelOrderController;
use App\Http\Controllers\Order\CreateBuyOrderController;
use App\Http\Controllers\Order\CreateSellOrderController;
use App\Http\Controllers\Order\ListOrderBookController;
use App\Http\Controllers\Order\ListUserOrdersController;
use App\Http\Controllers\User\RegisterController;
use App\Http\Controllers\User\UserController;
use Illuminate\Support\Facades\Route;

Route::post('/login', LoginController::class);
Route::post('/register', RegisterController::class);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', LogoutController::class);
    Route::get('/user', [UserController::class, 'show']);

    Route::get('/orders', ListUserOrdersController::class);
    Route::post('/orders/buy', CreateBuyOrderController::class);
    Route::post('/orders/sell', CreateSellOrderController::class);
    Route::post('/orders/{order}/cancel', CancelOrderController::class);
    Route::get('/orderbook/{symbol}', ListOrderBookController::class);
});

Broadcast::routes(['middleware' => ['auth:sanctum']]);
