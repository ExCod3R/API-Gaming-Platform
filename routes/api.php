<?php

use App\Enums\RoleEnum;
use App\Http\Controllers\AdController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\BlogCategoryController;
use App\Http\Controllers\BlogPostController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\GameController;
use App\Http\Controllers\PackageController;
use App\Http\Controllers\ScoreController;
use App\Http\Controllers\VoteController;
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

// Player Auth
Route::post('/login', [AuthController::class, 'login']);
Route::post('/social/login', [AuthController::class, 'socialLogin']);
Route::post('/register', [AuthController::class, 'register']);
Route::post('/password-reset', [PasswordResetController::class, 'reset']);
Route::post('/verify-otp', [PasswordResetController::class, 'verifyOtp']);

Route::get('/channel/{channelId}/games', [GameController::class, 'index']);
Route::get('/channel/{channelId}/games/{slug}', [GameController::class, 'show']);

Route::get('/channel/{channelId}/packages', [PackageController::class, 'index']);

Route::get('/channel/{channelId}/blog-categories', [BlogCategoryController::class, 'index']);

Route::get('/channel/{channelId}/blog-posts', [BlogPostController::class, 'index']);
Route::get('/channel/{channelId}/blog-posts/random', [BlogPostController::class, 'random']);
Route::get('/channel/{channelId}/blog-posts/{slug}', [BlogPostController::class, 'show']);


Route::post('/contact-us', [ContactController::class, 'index']);

Route::group(['middleware' => ['auth:sanctum', 'role:' . RoleEnum::PLAYER->value]], function () {
    // Client Auth
    Route::get('/me', [AuthController::class, 'show']);
    Route::put('/me', [AuthController::class, 'update']);

    Route::get('/game-path', [GameController::class, 'gamePath']);

    Route::get('/scores', [ScoreController::class, 'index']);
    Route::post('/scores', [ScoreController::class, 'store']);

    Route::apiResource('/votes', VoteController::class)->only(['index', 'store']);
});

Route::get('/channel/{channelId}/ads', [AdController::class, 'index']);

Route::get('/channel/{channelId}/scores/image', [ScoreController::class, 'image']);
