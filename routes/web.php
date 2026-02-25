<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\FolderController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

$redirectIfAuthenticated = function ($view) {
    if (! Auth::check()) {
        return view($view);
    }

    return Auth::user()->is_admin
        ? redirect()->to('/nova/resources/users')
        : redirect()->route('dashboard');
};

Route::get('/', fn () => $redirectIfAuthenticated('index'))->name('home');
Route::get('/signin', fn () => $redirectIfAuthenticated('login'))->name('signin');

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
        Route::patch('/folders/move', [FolderController::class, 'move'])->name('folders.move');
        Route::patch('/folders/{folder}', [FolderController::class, 'update'])->name('folders.update');
        Route::delete('/folders', [FolderController::class, 'delete'])->name('folders.delete');

        Route::post('/files', [FileController::class, 'store'])->name('files.store');
        Route::patch('/files/move', [FileController::class, 'move'])->name('files.move');
        Route::delete('/files', [FileController::class, 'destroy'])->name('files.destroy');
        Route::get('/files/{file}/download', [FileController::class, 'download'])->name('files.download');
    });
});

// Vue SPA catch-all — serves the built frontend for /spa/* routes
Route::get('/spa/{any?}', fn () => file_get_contents(public_path('spa/index.html')))->where('any', '.*');
