<?php

use App\Http\Controllers\Portal\ModulePageController;
use Illuminate\Support\Facades\Route;

Route::get('/platform-data-management', ModulePageController::class)
    ->defaults('portal', 'admin')
    ->defaults('module', 'platform_data_management')
    ->where('slug', 'platform-data-management')
    ->name('platform-data-management.index');
