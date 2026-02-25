<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\FolderController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Public auth routes
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

// Authenticated routes
Route::middleware('auth:sanctum')->group(function () {

    Route::get('/user', fn (Request $request) => response()->json($request->user()));

    // Regular user routes (non-admin only)
    Route::middleware('user')->group(function () {
        Route::post('/logout', [AuthController::class, 'userLogout']);
        Route::get('/dashboard', [UserController::class, 'dashboard']);

        Route::post('/folders', [FolderController::class, 'store']);
        Route::patch('/folders/move', [FolderController::class, 'move']); // must be before {folder}
        Route::patch('/folders/{folder}', [FolderController::class, 'update']);
        Route::delete('/folders', [FolderController::class, 'delete']);

        Route::post('/files', [FileController::class, 'store']);
        Route::patch('/files/move', [FileController::class, 'move']);
        Route::delete('/files', [FileController::class, 'destroy']);
        Route::get('/files/{file}/download', [FileController::class, 'download']);
    });
});
