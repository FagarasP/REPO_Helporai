<?php

use App\Http\Controllers\Portal\ModulePageController;
use Illuminate\Support\Facades\Route;

Route::get('/job-offers', ModulePageController::class)
    ->defaults('portal', 'freelancer')
    ->defaults('module', 'job_offers')
    ->where('slug', 'job-offers')
    ->name('job-offers.index');
