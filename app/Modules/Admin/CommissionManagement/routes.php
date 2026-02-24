<?php

use App\Http\Controllers\Portal\ModulePageController;
use Illuminate\Support\Facades\Route;

Route::get('/commission-management', ModulePageController::class)
    ->defaults('portal', 'admin')
    ->defaults('module', 'commission_management')
    ->where('slug', 'commission-management')
    ->name('commission-management.index');
