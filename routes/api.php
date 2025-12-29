<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\HospitalController;
use App\Http\Controllers\PatientController;
use Illuminate\Support\Facades\Route;

Route::get('/me', [HomeController::class, 'me']);

Route::group(['middleware' => 'web'], function () {
    Auth::routes();

    Route::get('stat', [HomeController::class, 'stat']);

    Route::apiResource('hospital', HospitalController::class)->except('show');
    Route::apiResource('patient', PatientController::class)->except('show');
});