<?php

use App\Http\Controllers\API\v1\AuthController;
use App\Http\Controllers\API\v1\GameController;
use App\Http\Controllers\API\v1\GenreController;
use App\Http\Controllers\API\v1\MovieController;
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

Route::prefix('/v1')->middleware('force-response.json')->group(function () {
    Route::prefix('/auth')->group(function () {
        Route::post('/register', [AuthController::class, 'register'])
            ->name('api.v1.auth.register')
            ->middleware("throttle:3,1");
        Route::post('/login', [AuthController::class, 'login'])
            ->name('api.v1.auth.login')
            ->middleware("throttle:6,2");

        Route::post('/forgot-password', [AuthController::class, 'forgotPassword'])
            ->name('api.v1.auth.forgot-password')
            ->middleware("throttle:3,2");
        Route::post('/reset-password/{email}/{token}', [AuthController::class, 'resetPassword'])
            ->name('api.v1.auth.reset-password')
            ->middleware("throttle:3,2");

        Route::middleware('auth:sanctum')->group(function () {
            Route::get('/user', [AuthController::class, 'user'])
                ->name('api.v1.auth.user');
            Route::post('/logout', [AuthController::class, 'logout'])
                ->name('api.v1.auth.logout');
            Route::post('/change-password', [AuthController::class, 'changePassword'])
                ->name('api.v1.auth.change-password');
        });
    });

    Route::middleware('auth:sanctum')->group(function () {
        Route::apiResource('games', GameController::class)->names([
            'index' => 'api.v1.games.index',
            'store' => 'api.v1.games.store',
            'show' => 'api.v1.games.show',
            'update' => 'api.v1.games.update',
            'destroy' => 'api.v1.games.destroy',
        ]);

        Route::apiResource('movies', MovieController::class)->names([
            'index' => 'api.v1.movies.index',
            'store' => 'api.v1.movies.store',
            'show' => 'api.v1.movies.show',
            'update' => 'api.v1.movies.update',
            'destroy' => 'api.v1.movies.destroy',
        ]);

        Route::apiResource('genres', GenreController::class)->names([
            'index' => 'api.v1.genres.index',
            'store' => 'api.v1.genres.store',
            'show' => 'api.v1.genres.show',
            'update' => 'api.v1.genres.update',
            'destroy' => 'api.v1.genres.destroy',
        ]);
    });
});
