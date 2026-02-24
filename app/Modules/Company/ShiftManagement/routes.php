<?php

use App\Http\Controllers\Portal\ModulePageController;
use Illuminate\Support\Facades\Route;

Route::get('/shift-management', ModulePageController::class)
    ->defaults('portal', 'company')
    ->defaults('module', 'shift_management')
    ->where('slug', 'shift-management')
    ->name('shift-management.index');
