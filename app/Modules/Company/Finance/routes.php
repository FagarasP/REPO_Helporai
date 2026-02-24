<?php

use App\Http\Controllers\Portal\ModulePageController;
use Illuminate\Support\Facades\Route;

Route::get('/finance', ModulePageController::class)
    ->defaults('portal', 'company')
    ->defaults('module', 'finance')
    ->where('slug', 'finance')
    ->name('finance.index');
