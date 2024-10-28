<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CarController;



Route::middleware('validateToken')->group(function () {
    Route::get('/cars', [CarController::class, 'getAllCars']);
    Route::post('/cars', [CarController::class, 'createCar']);
    Route::get('/cars/{id}', [CarController::class, 'getCar']);
    Route::put('/cars/{id}', [CarController::class, 'updateCar']);
    Route::delete('/cars/{id}', [CarController::class, 'deleteCar']);
});
