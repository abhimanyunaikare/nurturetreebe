<?php

use App\Http\Controllers\TreeController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PushTokenController;

// User Routes

Route::middleware(['auth:sanctum', 'role:admin'])->group(function () 
{
    // Route::get('/users', [UserController::class, 'index']);  // Get all users
    // Route::get('/users/{id}', [UserController::class, 'show']); // Get single user}

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
Route::get('/treesByUserId', [TreeController::class, 'showByUser']); // List all trees
Route::get('/trees/{id}', [TreeController::class, 'show']); // View a specific tree
Route::put('/trees/{id}/status', [TreeController::class, 'updateStatus']);

// Authentication Routes

Route::post('/register', [UserController::class, 'register']);
Route::get('/user', [UserController::class, 'getUser']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

Route::get('/users', [UserController::class, 'index']);  // Get all users
Route::get('/users/{id}', [UserController::class, 'show']); // Get single user}

Route::post('/push-token', [PushTokenController::class, 'store']);
Route::post('/send-push', [NotificationController::class, 'sendPush']);
