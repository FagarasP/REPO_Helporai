<?php

namespace App\Http\Controllers;

use Inertia\Inertia;

class CompanyController extends Controller
{
    public function dashboard()
    {
        return Inertia::render('Company/Dashboard');
    }

    public function projectManagement()
    {
        return redirect()->route('company.projects.index');
    }

    public function shiftManagement()
    {
        return Inertia::render('Company/ShiftManagement');
    }

    public function workforceScounting()
    {
        return Inertia::render('Company/Workforce/Scounting');
    }

    public function workforceRecruitment()
    {
        return Inertia::render('Company/Workforce/Recruitment');
    }

    public function workforceWatchlist()
    {
        return Inertia::render('Company/Workforce/Watchlist');
    }

    public function financePaymentEntry()
    {
        return Inertia::render('Company/Finance/PaymentEntry');
    }

    public function financeWalletPage()
    {
        return Inertia::render('Company/Finance/WalletPage');
    }

    public function financeInvoicePage()
    {
        return Inertia::render('Company/Finance/InvoicePage');
    }
}
