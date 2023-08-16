<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
 */

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// public routes
Route::post('/socialAuth', [AuthController::class, 'socialAuth']);
Route::post('/signIn', [AuthController::class, 'signIn']);
Route::post('/signUp', [AuthController::class, 'signUp']);

// private routes
Route::group([
    'middleware' => 'auth:sanctum',
], function () {
    // user
    Route::prefix('user')->group(function () {
        Route::get('/', [UserController::class, 'fetchUser']);
    });

});

Route::prefix('posts')->group(function () {
    Route::get('/', [PostController::class, 'fetchAllPosts']);
    Route::post('/create', [PostController::class, 'createPost']);
    Route::post('/updateLikeStatus', [PostController::class, 'updateLikeStatus']);
});
