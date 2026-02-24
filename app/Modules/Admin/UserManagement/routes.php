<?php

use App\Http\Controllers\Portal\ModulePageController;
use Illuminate\Support\Facades\Route;

Route::get('/user-management', ModulePageController::class)
    ->defaults('portal', 'admin')
    ->defaults('module', 'user_management')
    ->where('slug', 'user-management')
    ->name('user-management.index');
