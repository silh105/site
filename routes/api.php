<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\PostController;
use App\Http\Controllers\Api\ChatController;

Route::get('/check-login', [AuthController::class, 'checkLogin']);
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/categories', [CategoryController::class, 'index']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::apiResource('posts', PostController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::get('/chats', [ChatController::class, 'index']);
    Route::get('/chats/{chat}', [ChatController::class, 'show']);
    Route::post('/chats/{chat}/messages', [ChatController::class, 'sendMessage']);
    Route::post('/chats/{chat}/read', [ChatController::class, 'markAsRead']);
    Route::delete('/messages/{message}', [ChatController::class, 'deleteMessage']);
});