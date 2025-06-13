<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\TodoController;
use Illuminate\Support\Facades\Route;

Route::get('/', [TodoController::class, 'index'])->name('home')->middleware('auth');
Route::get('login', [AuthController::class, 'login'])->name('login');
Route::post('/login-post', [AuthController::class, 'loginPost'])->name('login.post');
Route::get('register', [AuthController::class, 'register'])->name('register');
Route::post('/register-post', [AuthController::class, 'registerPost'])->name('register.post');
Route::get('logout', [AuthController::class, 'logout'])->name('logout');

// Todo Routes (protected by auth middleware)
Route::middleware('auth')->group(function () {
    Route::get('/todos/create', [TodoController::class, 'create'])->name('todos.create');
    Route::post('/todos', [TodoController::class, 'store'])->name('todos.store');
    Route::get('/todos/{todo}/edit', [TodoController::class, 'edit'])->name('todos.edit');
    Route::put('/todos/{todo}', [TodoController::class, 'update'])->name('todos.update');
    Route::delete('/todos/{todo}', [TodoController::class, 'destroy'])->name('todos.destroy');
    Route::patch('/todos/{todo}/toggle', [TodoController::class, 'toggleStatus'])->name('todos.toggle');
});
