<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\HospitalController;
use Illuminate\Support\Facades\Route;

Route::get('/me', [HomeController::class, 'me']);

Route::group(['middleware' => 'web'], function () {
    Auth::routes();

    Route::apiResource('hospital', HospitalController::class)->except('show');
});