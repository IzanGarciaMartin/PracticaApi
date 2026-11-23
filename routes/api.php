<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AcademyController;
use App\Http\Controllers\CourseController;

// Rutas públicas
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::get(
    '/academy/{academy}/courses',
    [CourseController::class, 'academyCourses']
);

// Rutas protegidas
Route::middleware('auth:sanctum')->group(function () {

    // Autenticación
    Route::post('/logout', [AuthController::class, 'logout']);

    // Gestión de academias
    Route::apiResource('academies', AcademyController::class);

    //Gestión de cursos
    Route::apiResource('courses', CourseController::class);

});
