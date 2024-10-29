<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\TransactionController;


Route::fallback(function(){
    return response()->json(['message' => 'Page Not Found'], 404);
});

Route::prefix('v1')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);
    
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/transaction', [TransactionController::class, 'store']);
        Route::get('/balance', [UserController::class, 'balance']);
    }); 
});
