<?php

use App\Http\Controllers\Portal\ModulePageController;
use Illuminate\Support\Facades\Route;

Route::get('/project-management', ModulePageController::class)
    ->defaults('portal', 'company')
    ->defaults('module', 'project_management')
    ->where('slug', 'project-management')
    ->name('project-management.index');
