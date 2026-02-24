<?php

use App\Http\Controllers\Portal\ModulePageController;
use Illuminate\Support\Facades\Route;

Route::get('/dashboard', ModulePageController::class)
    ->defaults('portal', 'admin')
    ->defaults('module', 'dashboard')
    ->where('slug', 'dashboard')
    ->name('dashboard.index');
