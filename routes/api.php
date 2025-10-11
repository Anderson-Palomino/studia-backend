<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\TaskController;

Route::get('/ping', function () {
    return response()->json(['message' => 'pong ✅']);
});
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/perfil', [AuthController::class, 'perfil']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user/courses', [CourseController::class, 'getUserCourses']);
    Route::apiResource('/courses', CourseController::class);
    Route::apiResource('/tasks', TaskController::class);
});
