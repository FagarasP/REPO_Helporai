<?php

use App\Http\Controllers\Portal\ModulePageController;
use Illuminate\Support\Facades\Route;

Route::get('/project-settings', ModulePageController::class)
    ->defaults('portal', 'admin')
    ->defaults('module', 'project_settings')
    ->where('slug', 'project-settings')
    ->name('project-settings.index');
