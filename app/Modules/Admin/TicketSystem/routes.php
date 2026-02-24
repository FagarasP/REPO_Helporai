<?php

use App\Http\Controllers\Portal\ModulePageController;
use Illuminate\Support\Facades\Route;

Route::get('/ticket-system', ModulePageController::class)
    ->defaults('portal', 'admin')
    ->defaults('module', 'ticket_system')
    ->where('slug', 'ticket-system')
    ->name('ticket-system.index');
