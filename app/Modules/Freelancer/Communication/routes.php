<?php

use App\Http\Controllers\Portal\ModulePageController;
use Illuminate\Support\Facades\Route;

Route::get('/communication', ModulePageController::class)
    ->defaults('portal', 'freelancer')
    ->defaults('module', 'communication')
    ->where('slug', 'communication')
    ->name('communication.index');
