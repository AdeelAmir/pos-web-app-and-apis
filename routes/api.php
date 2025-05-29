<?php

use App\Http\Controllers\API\APIController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/login', [APIController::class, 'login']);
Route::post('/forget-password', [APIController::class, 'forgetPassword']);

Route::post('/cities/all', [APIController::class, 'getAllCities']);
Route::post('/shop/all', [APIController::class, 'getAllShops']);
Route::post('/shop/search', [APIController::class, 'searchShop']);

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::group(['middleware' => ['auth:sanctum']], function () {
    // PROFILE
    Route::post('/changePassword', [APIController::class, 'changePassword']);
    Route::post('/edit-profile', [APIController::class, 'editProfile']);
    Route::post('/logout', [APIController::class, 'logout']);
    Route::post('/deleteAccount', [APIController::class, 'deleteAccount']);
    Route::post('/getUserDetails', [APIController::class, 'getUserDetails']);

    // DASHBOARD
    Route::post('/dashboard', [APIController::class, 'getDashboard']);

    // PRODUCT
    Route::post('/product/all', [APIController::class, 'getAllProducts']);
    Route::post('/goods/all', [APIController::class, 'getAllGoods']);

    // SHOP
    Route::post('/shop/add', [APIController::class, 'addShop']);

    // ORDER
    Route::post('/order/add', [APIController::class, 'addOrder']);
    Route::post('/order/details', [APIController::class, 'orderDetails']);
    Route::post('/order/report', [APIController::class, 'getOrderReport']);

    // LOAN
    Route::post('/loan/report', [APIController::class, 'getLoanReport']);
    Route::post('/loan/partial-payment', [APIController::class, 'loanCollectPartialPayment']);
    Route::post('/loan/collect-money', [APIController::class, 'loanCollectMoney']);

    // SALE
    Route::post('/sale/report', [APIController::class, 'getSaleReport']);
    Route::post('/sale/report/detail', [APIController::class, 'getSaleReportDetails']);

    Route::post('/seller/targets', [APIController::class, 'getSellerTarget']);

    // DEMAND
    Route::post('/demand/all', [APIController::class, 'getAllDemands']);
    Route::post('/demand/add', [APIController::class, 'addDemand']);
    Route::post('/demand/edit', [APIController::class, 'editDemand']);
    Route::post('/demand/delete', [APIController::class, 'deleteDemand']);
});
