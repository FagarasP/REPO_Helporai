<?php

namespace App\Http\Controllers;

use Inertia\Inertia;

class OtherController extends Controller
{
    public function dashboard()
    {
        return Inertia::render('Other/Dashboard');
    }

    public function projectManagement()
    {
        return Inertia::render('Other/ProjectManagement');
    }

    public function shiftManagement()
    {
        return Inertia::render('Other/ShiftManagement');
    }

    public function workforceScounting()
    {
        return Inertia::render('Other/Workforce/Scounting');
    }

    public function workforceRecruitment()
    {
        return Inertia::render('Other/Workforce/Recruitment');
    }

    public function workforceWatchlist()
    {
        return Inertia::render('Other/Workforce/Watchlist');
    }

    public function financePaymentEntry()
    {
        return Inertia::render('Other/Finance/PaymentEntry');
    }

    public function financeWalletPage()
    {
        return Inertia::render('Other/Finance/WalletPage');
    }

    public function financeInvoicePage()
    {
        return Inertia::render('Other/Finance/InvoicePage');
    }
}
