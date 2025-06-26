<?php

use App\Http\Controllers\Api\AccountController;
use App\Http\Controllers\Api\ContactController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\PaymentController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:api', 'throttle:48,1'])->group(function () {
    // dashboard
    Route::prefix('dashboard')->controller(DashboardController::class)->group(function(){
        Route::get('detail', 'detail');
        Route::get('getTransactionDetail/{filter}', 'getTransactionDetail');
        Route::get('getAmountDetail/{filter}', 'getAmountDetail');
    });
    
    // account
    Route::prefix('account')->controller(AccountController::class)->group(function () {
        Route::get('detail', 'detail');
        Route::get('log-out', 'logOut');
        Route::get('log-out-all', 'logOutAll');
        Route::get('delete', 'delete');
    });

    // recent
    Route::prefix('recent')->controller(ContactController::class)->group(function () {
        Route::get('list/{currentPage}/{perPage}/{search?}', 'recentList');
    });

    // contact
    Route::prefix('contact')->controller(ContactController::class)->group(function () {
        Route::get('list/{currentPage}/{perPage}/{search?}', 'list');
        Route::get('find-new/{search}', 'findNew');
        Route::get('add-new/{fbid}', 'addNew');
    });

    // payment
    Route::prefix('payment')->controller(PaymentController::class)->group(function () {
        Route::get('to-user-detail/{toUserFbid}', 'getToUserDetail');
        Route::get('list/{toUserFbid}/{currentPage}/{perPage}', 'list');
        Route::post('pay', 'pay');
        Route::post('request', 'request');
        Route::post('pay-request', 'payRequest');
    });

    // transactions
    Route::prefix('transactions')->controller(PaymentController::class)->group(function () {
        Route::post('list/{currentPage}/{perPage}', 'transactionList');
    });

    // notifications
    Route::prefix('notifications')->controller(NotificationController::class)->group(function () {
        Route::get('new', 'new');
        Route::get('list/{currentPage}/{perPage}', 'list');
        Route::get('delete/{id}', 'delete');
        Route::get('delete-all', 'deleteAll');
    });
});
