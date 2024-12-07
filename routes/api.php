<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\FormationController;
use App\Http\Controllers\SchoolController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
Route::apiResource('categories', CategoryController::class);
Route::apiResource('schools', SchoolController::class);
// Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/admins', [AdminController::class, 'index']); // List admins
    Route::post('/admins', [AdminController::class, 'store']); // Create admin
    Route::get('/admins/{admin}', [AdminController::class, 'show']); // View admin
    Route::put('/admins/{admin}', [AdminController::class, 'update']); // Update admin
    Route::delete('/admins/{admin}', [AdminController::class, 'destroy']); // Delete admin
    Route::get('/permissions', [AdminController::class, 'getPermissions']); // List all permissions
// });
Route::apiResource('formations', FormationController::class);
