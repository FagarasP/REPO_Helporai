<?php

use App\Http\Controllers\Portal\ModulePageController;
use Illuminate\Support\Facades\Route;

Route::get('/shift-plan', ModulePageController::class)
    ->defaults('portal', 'freelancer')
    ->defaults('module', 'shift_plan')
    ->where('slug', 'shift-plan')
    ->name('shift-plan.index');
