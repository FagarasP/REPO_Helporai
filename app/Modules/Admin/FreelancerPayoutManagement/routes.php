<?php

use App\Http\Controllers\Portal\ModulePageController;
use Illuminate\Support\Facades\Route;

Route::get('/freelancer-payout-management', ModulePageController::class)
    ->defaults('portal', 'admin')
    ->defaults('module', 'freelancer_payout_management')
    ->where('slug', 'freelancer-payout-management')
    ->name('freelancer-payout-management.index');
