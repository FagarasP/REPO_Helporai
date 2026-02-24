<?php

use App\Http\Controllers\Portal\ModulePageController;
use Illuminate\Support\Facades\Route;

Route::get('/workforce-management', ModulePageController::class)
    ->defaults('portal', 'company')
    ->defaults('module', 'workforce_management')
    ->where('slug', 'workforce-management')
    ->name('workforce-management.index');
