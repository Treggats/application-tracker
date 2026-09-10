<?php

use App\Http\Controllers\ApplicationController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/applications', [ApplicationController::class, 'index'])
    ->name('applications.index');

Route::view('applications/create', 'applications.create')
    ->name('applications.create');

Route::get('/applications/{application}', [ApplicationController::class, 'show'])
    ->name('applications.show');

Route::post('applications', [ApplicationController::class, 'store'])
    ->name('applications.store');

Route::get('applications/{application}/edit', [ApplicationController::class, 'edit'])
    ->name('applications.edit');

Route::put('applications/{application}', [ApplicationController::class, 'update'])
    ->name('applications.update');

Route::delete('applications/{application}', [ApplicationController::class, 'destroy'])
    ->name('applications.destroy');
