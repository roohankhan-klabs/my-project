<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\FolderController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

$redirectIfAuthenticated = function ($view) {
    if (!Auth::check()) {
        return view($view);
    }
    return Auth::user()->is_admin
        ? redirect()->to('/nova/resources/users')
        : redirect()->route('dashboard');
};

Route::get('/', fn() => $redirectIfAuthenticated('index'))->name('home');
Route::get('/signin', fn() => $redirectIfAuthenticated('login'))->name('signin');

Route::post('/userSignin', [AuthController::class, 'login'])->name('userSignin');
Route::post('/register', [AuthController::class, 'register'])->name('register');

Route::middleware('auth')->group(function () {
    Route::middleware('admin')->group(function () {
        // Admin routes, Nova access only
    });

    Route::middleware('user')->group(function () {
        Route::post('/userLogout', [AuthController::class, 'userLogout'])->name('userLogout');
        Route::get('/dashboard', [UserController::class, 'dashboard'])->name('dashboard');
        
        Route::post('/folders', [FolderController::class, 'store'])->name('folders.store');
        Route::patch('/folders/{folder}', [FolderController::class, 'update'])->name('folders.update');
        Route::delete('/folders', [FolderController::class, 'delete'])->name('folders.delete');

        Route::post('/files', [FileController::class, 'store'])->name('files.store');
        Route::delete('/files', [FileController::class, 'destroy'])->name('files.destroy');
    });
});
