<?php

use App\Http\Controllers\TreeController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;

// User Routes

Route::middleware(['auth:sanctum', 'role:admin'])->group(function () 
{
    Route::get('/users', [UserController::class, 'index']);  // Get all users
    Route::get('/users/{id}', [UserController::class, 'show']); // Get single user}

});

// Trees Routes

Route::middleware(['auth:sanctum', 'role:user'])->group(function () 
{
    
});

Route::middleware(['auth:sanctum', 'role:user'])->group(function () 
{
    
});
Route::post('/trees', [TreeController::class, 'store']); // Plant a new tree
Route::put('/trees/{id}', [TreeController::class, 'update']); 
Route::delete('/trees/{id}', [TreeController::class, 'destroy']); 
Route::post('/trees/{id}/nurture', [TreeController::class, 'nurture']); // Nurture a tree
Route::get('/trees', [TreeController::class, 'index']); // List all trees
Route::get('/trees/{id}', [TreeController::class, 'show']); // View a specific tree

// Authentication Routes

Route::post('/register', [AuthController::class, 'register']);
Route::get('/user', [AuthController::class, 'user']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');