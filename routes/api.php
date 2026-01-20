<?php

use App\Http\Controllers\AuthController;
use App\services\password\PasswordResetController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->json(['message' => 'Hello world!']);
});

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('jwt')->group(function () {
    Route::get('/user', [AuthController::class, 'getUser']);
    Route::put('/user', [AuthController::class, 'updateUser']);
    Route::post('/logout', [AuthController::class, 'logout']);
});

// Forget Password
Route::post('forget_password', [PasswordResetController::class, 'send_reset_code']);
Route::post('reset_password', [PasswordResetController::class, 'reset_password']);

// Category Routes
Route::get('/categories', [App\Http\Controllers\CategoryController::class, 'index']);

// Product Routes
Route::get('/products', [App\Http\Controllers\ProductController::class, 'index']);
Route::get('/products/{id}', [App\Http\Controllers\ProductController::class, 'show']);
Route::get('/categories/{category_id}/products', [App\Http\Controllers\ProductController::class, 'get_products_by_category']);