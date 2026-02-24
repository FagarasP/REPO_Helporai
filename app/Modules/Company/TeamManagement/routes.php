<?php

use App\Http\Controllers\Portal\ModulePageController;
use Illuminate\Support\Facades\Route;

Route::get('/team-management', ModulePageController::class)
    ->defaults('portal', 'company')
    ->defaults('module', 'team_management')
    ->where('slug', 'team-management')
    ->name('team-management.index');
