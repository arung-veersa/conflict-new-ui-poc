<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ChartController;

Route::get('/', [ChartController::class, 'index']);
Route::post('/chart/load-data', [ChartController::class, 'loadChartData']);
Route::get('/chart/test-connection', [ChartController::class, 'testConnection']);
