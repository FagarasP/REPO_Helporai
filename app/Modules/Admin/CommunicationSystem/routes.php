<?php

use App\Http\Controllers\Portal\ModulePageController;
use Illuminate\Support\Facades\Route;

Route::get('/communication-system', ModulePageController::class)
    ->defaults('portal', 'admin')
    ->defaults('module', 'communication_system')
    ->where('slug', 'communication-system')
    ->name('communication-system.index');
