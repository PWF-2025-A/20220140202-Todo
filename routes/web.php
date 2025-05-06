<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TodoController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// Halaman awal
Route::get('/', function () {
    return view('welcome');
});

// Dashboard (perlu login dan verifikasi)
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Group route yang memerlukan autentikasi
Route::middleware('auth')->group(function () {

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Todo (akses umum)
    Route::get('/todo', [TodoController::class, 'index'])->name('todo.index');
    Route::patch('/todo/{todo}/complete', [TodoController::class, 'complete'])->name('todo.complete');
    Route::patch('/todo/{todo}/uncomplete', [TodoController::class, 'uncomplete'])->name('todo.uncomplete');
    Route::delete('/todo', [TodoController::class, 'destroyCompleted'])->name('todo.deleteallcompleted');
    Route::delete('/todo/{todo}', [TodoController::class, 'destroy'])->name('todo.destroy');

    // User (untuk tampilan daftar user, bisa dibatasi pakai policy atau middleware)
    Route::get('/user', [UserController::class, 'index'])->name('user.index');

    // Admin khusus
    Route::middleware('admin')->group(function () {
        Route::resource('todo', TodoController::class)->except(['show', 'index', 'destroy']);
        Route::get('/todo/create', [TodoController::class, 'create'])->name('todo.create');
        Route::post('/todo', [TodoController::class, 'store'])->name('todo.store');
        Route::get('/todo/{todo}/edit', [TodoController::class, 'edit'])->name('todo.edit');
        Route::put('/todo/{todo}', [TodoController::class, 'update'])->name('todo.update');

        Route::patch('/user/{user}/makeadmin', [UserController::class, 'makeadmin'])->name('user.makeadmin');
        Route::patch('/user/{user}/removeadmin', [UserController::class, 'removeadmin'])->name('user.removeadmin');
        Route::delete('/user/{user}', [UserController::class, 'destroy'])->name('user.destroy');
    });
});

// Routing autentikasi
require __DIR__.'/auth.php';
