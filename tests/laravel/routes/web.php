<?php

use App\Http\Controllers;
use Illuminate\Support\Facades\Route;

Route::get('test', [Controllers\TestController::class, 'index']);
Route::get('test/link/{id?}', [Controllers\TestController::class, 'link']);
Route::get('test/n-href/{id?}', [Controllers\TestController::class, 'nHref']);
Route::get('test/detail/{id?}', [Controllers\TestController::class, 'detail']);
Route::get('admin/test/detail', [Controllers\Admin\TestController::class, 'detail']);
