<?php

use App\Http\Controllers\Admin\AdminCommentController;
use App\Http\Controllers\Admin\AdminPostController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Authentication\AuthenticationController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\PostController;
use App\Http\Middleware\API\GuestMiddleware;
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

Route::get('swagger', function () {
    return response()->file(public_path() . '/swagger.json');
});

Route::group(['prefix' => 'v1'], function () {

        Route::middleware(GuestMiddleware::class)->group(function () {
            Route::post('/register', [AuthenticationController::class, 'register'])->name('register');
            Route::post('/login', [AuthenticationController::class, 'login'])->name('login');
        });

        Route::middleware(['isUserBanned', 'auth:api'])->group(function () {
            Route::post('/logout', [AuthenticationController::class, 'logout'])->name('logout');
            Route::get('/profile', [AuthenticationController::class, 'profile'])->name('profile');

            Route::delete('/posts/{post}', [PostController::class, 'forceDelete'])
                ->name('posts.force-delete');
            Route::apiResource('posts', PostController::class)->only([
                'store', 'update'
            ]);

            Route::apiResource('comments', CommentController::class)
                ->only('store');

            Route::post('posts/{post}/soft-delete', [PostController::class, 'softDelete'])
                ->name('posts.soft-delete');
            Route::post('posts/{post}/restore', [PostController::class, 'restore'])
                ->name('posts.restore');
            Route::post('posts/{post}/publish', [PostController::class, 'publish'])
                ->name('posts.publish');
            Route::post('posts/{post}/unpublish', [PostController::class, 'unpublish'])
                ->name('posts.unpublish');

            Route::middleware(['isAdmin'])->group(function () {
                Route::post('/admin/users/{user}/ban', [AdminUserController::class, 'ban'])
                    ->name('admin.users.ban');
                Route::post('/admin/users/{user}/restore', [AdminUserController::class, 'restore'])
                    ->name('admin.users.restore');

                Route::get('/admin/posts', [AdminPostController::class, 'index'])
                    ->name('admin.posts.index');
                Route::get('/admin/posts/{post}', [AdminPostController::class, 'show'])
                    ->name('admin.posts.show');
                Route::delete('/admin/posts/{post}', [AdminPostController::class, 'forceDelete'])
                    ->name('admin.posts.force-delete');

                Route::delete('/admin/comments/{comment}', [AdminCommentController::class, 'forceDelete'])
                    ->name('admin.comments.force-delete');
            });
        });

    Route::apiResource('posts', PostController::class)->only(['index', 'show']);
});
