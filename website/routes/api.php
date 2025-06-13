<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ToDoController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/todos', [ToDoController::class, 'postTodo']);
    Route::get('/todos', [ToDoController::class, 'toDos']);
    Route::put('/todos/{id}', [ToDoController::class, 'updateToDo']);
    Route::patch('/todos/{id}/status', [ToDoController::class, 'updateStatus']); // Route baru untuk update status
    Route::delete('/todos/{id}', [ToDoController::class, 'deleteToDo']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/todos/update-late-status', [ToDoController::class, 'updateLateStatus']);
});