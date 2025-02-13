<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\EmployeController;
use App\Http\Controllers\EtudiantController;
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
Route::apiResource('employes', EmployeController::class);
Route::apiResource('etudiants', EtudiantController::class);
Route::post('/etudiants/{id}/convert-to-employee', [EtudiantController::class, 'convertToEmployee']);
Route::post('/employes/{id}/convert-to-stage', [EmployeController::class, 'convertToStage']);
// Route::get('/stagiaires', [EmployeController::class, 'stagiaires'])->name('stagiaires');
Route::get('/stagiaires', [EmployeController::class, 'index'])->name('stagiaires');
