<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\ApplicationController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DashboardController;

use App\Enums\ApplicationStatus;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

//Public routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

//Protected routes; Need login
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/dashboard', [DashboardController::class, 'index']);

    Route::get('/applications', [ApplicationController::class, 'index']); //List all existing applications
    Route::post('/applications', [ApplicationController::class, 'store']); //Create new application
    Route::get('/applications/{application}', [ApplicationController::class, 'show']); //View specific application
    Route::patch('/applications/{application}', [ApplicationController::class, 'update']); //Update specific application
    Route::delete('/applications/{application}', [ApplicationController::class, 'destroy']); //Delete specific application
});

Route::get('/application-statuses', function () {
    return collect(ApplicationStatus::cases())
        ->map(fn ($status) => [
            'value' => $status->value,
            'label' => $status->label(),
        ])
        ->values();
});
