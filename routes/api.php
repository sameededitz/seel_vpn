<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\PurchaseController;
use App\Http\Controllers\Api\ResourceController;

Route::middleware('guest')->group(function () {
    Route::post('/signup', [AuthController::class, 'signup'])->name('api.signup');

    Route::post('/login', [AuthController::class, 'login'])->name('api.login');

});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', [UserController::class, 'user'])->name('api.user');

    Route::post('/user/update', [UserController::class, 'updateProfile'])->name('api.profile.update');

    Route::post('/user/update-password', [UserController::class, 'updatePassword'])->name('api.profile.update.password');

    Route::delete('/user/delete', [UserController::class, 'deleteAccount'])->name('api.profile.delete');

    Route::post('/logout', [AuthController::class, 'logout'])->name('api.logout');

    Route::get('/purchase/active', [PurchaseController::class, 'active'])->name('api.plan.active');

    Route::get('/purchase/history', [PurchaseController::class, 'history'])->name('api.plan.history');

    Route::post('/purchase/add', [PurchaseController::class, 'addPurchase'])->name('api.add.purchase');

    Route::get('/servers', [ResourceController::class, 'servers'])->name('api.servers');

    Route::get('/nearest-server', [ResourceController::class, 'nearestServer']);

    Route::post('/feedback/store', [ResourceController::class, 'addFeedback'])->name('api.feedback.add');
});
Route::get('/vps-servers', [ResourceController::class, 'vpsServers']);

Route::get('/plans', [ResourceController::class, 'plans']);