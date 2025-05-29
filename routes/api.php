<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\SocialController;
use App\Http\Controllers\Api\TicketController;
use App\Http\Controllers\Api\AccountController;
use App\Http\Controllers\Api\PurchaseController;
use App\Http\Controllers\Api\ResourceController;
use App\Http\Controllers\Api\BillingAddressController;

Route::middleware('guest', 'throttle:api')->group(function () {
    Route::post('/signup', [AuthController::class, 'signup'])->name('api.signup');
    Route::post('/login', [AuthController::class, 'login'])->name('api.login');

    Route::post('/login/google', [SocialController::class, 'google'])->name('api.login.google');
    Route::post('/login/apple', [SocialController::class, 'apple'])->name('api.login.apple');

    Route::post('/email/resend-verification', [AccountController::class, 'resendEmail'])->name('api.verify.resend');
    Route::get('/email/verify/{id}/{hash}', [AccountController::class, 'verifyEmail'])->name('verification.verify');

    Route::post('/forgot-password', [AccountController::class, 'sendResetLink'])->name('api.password.reset');
    Route::post('/reset-password', [AccountController::class, 'resetPassword'])->name('api.password.update');
});

Route::middleware('auth:sanctum', 'throttle:api')->group(function () {
    Route::get('/user', [UserController::class, 'user'])->name('api.user');
    Route::post('/user/update', [UserController::class, 'updateProfile'])->name('api.profile.update');
    Route::post('/user/update-password', [UserController::class, 'updatePassword'])->name('api.profile.update.password');
    Route::delete('/user/delete', [UserController::class, 'deleteAccount'])->name('api.profile.delete');

    Route::post('/logout', [AuthController::class, 'logout'])->name('api.logout');

    Route::get('/purchase/active', [PurchaseController::class, 'active'])->name('api.plan.active');
    Route::get('/purchase/history', [PurchaseController::class, 'history'])->name('api.plan.history');
    Route::get('/purchase/{id}', [PurchaseController::class, 'viewPurchase'])->name('api.plan.show');
    Route::post('/purchase/add', [PurchaseController::class, 'addPurchase'])->name('api.add.purchase');
    Route::post('/apply-promo-code', [PurchaseController::class, 'apply'])->name('api.apply.promo.code');

    Route::get('/servers', [ResourceController::class, 'servers'])->name('api.servers');
    Route::get('/nearest-server', [ResourceController::class, 'nearestServer']);

    Route::get('/tickets', [TicketController::class, 'index'])->name('api.tickets.index');
    Route::get('/ticket/{id}', [TicketController::class, 'show'])->name('api.tickets.show');
    Route::post('/ticket/create', [TicketController::class, 'store'])->name('api.tickets.store');
    Route::post('/ticket/{ticketId}/reply', [TicketController::class, 'reply'])->name('api.tickets.reply');
    Route::post('/ticket/{ticketId}/close', [TicketController::class, 'close'])->name('api.tickets.close');
    Route::post('/tickets/{ticketId}/priority', [TicketController::class, 'priority'])->name('api.tickets.priority');
    Route::delete('/ticket/{ticketId}/delete', [TicketController::class, 'destroy'])->name('api.tickets.delete');

    Route::get('/billing-address', [BillingAddressController::class, 'show'])->name('api.billing.address.show');
    Route::post('/billing-address/store', [BillingAddressController::class, 'store'])->name('api.billing.address.store');
    Route::delete('/billing-address/delete', [BillingAddressController::class, 'destroy'])->name('api.billing.address.delete');
});

Route::post('/feedback/store', [ResourceController::class, 'addFeedback'])->name('api.feedback.add');

Route::get('/vps-servers', [ResourceController::class, 'vpsServers']);

Route::get('/plans', [ResourceController::class, 'plans']);

Route::get('/options', [ResourceController::class, 'options']);
