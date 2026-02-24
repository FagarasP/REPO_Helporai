<?php

use App\Http\Controllers\Portal\ModulePageController;
use Illuminate\Support\Facades\Route;

Route::get('/my-jobs', ModulePageController::class)
    ->defaults('portal', 'freelancer')
    ->defaults('module', 'my_jobs')
    ->where('slug', 'my-jobs')
    ->name('my-jobs.index');
