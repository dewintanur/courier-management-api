<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CourierController;

Route::apiResource('couriers', CourierController::class);