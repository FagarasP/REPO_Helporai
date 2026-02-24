<?php

use App\Http\Controllers\Portal\ModulePageController;
use Illuminate\Support\Facades\Route;

Route::get('/pending-company-payments', ModulePageController::class)
    ->defaults('portal', 'admin')
    ->defaults('module', 'pending_company_payments')
    ->where('slug', 'pending-company-payments')
    ->name('pending-company-payments.index');
