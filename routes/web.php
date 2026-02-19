<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\FolderController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('index');
})->name('home');

Route::get('/signin', action: function () {
    return view('login');
})->name('signin');

Route::post('/userSignin', [AuthController::class, 'login'])->name('userSignin');
Route::post('/register', [AuthController::class, 'register'])->name('register');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [UserController::class, 'drive'])->name('dashboard');

    Route::post('/folders', [FolderController::class, 'store'])->name('folders.store');
    Route::patch('/folders/{folder}', [FolderController::class, 'update'])->name('folders.update');
    Route::delete('/folders', [FolderController::class, 'delete'])->name('folders.delete');
    Route::post('/files', [FileController::class, 'store'])->name('files.store');
    Route::delete('/files', [FileController::class, 'destroy'])->name('files.destroy');
});
