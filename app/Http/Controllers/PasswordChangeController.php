<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class PasswordChangeController extends Controller
{
    public function show()
    {
        return view('auth.change-password');
    }

    public function update(Request $request)
    {
        $request->validate([
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = auth()->user();
        $user->password = Hash::make($request->password);
        $user->must_change_password = false;
        $user->save();

        // Redirect based on user role
        if ($user->isAdmin()) {
            // Redirect admin to OM dashboard
            $redirectRoute = 'admin.panel';
        } elseif ($user->role === 'company') {
            $redirectRoute = 'company.dashboard';
        } elseif ($user->role === 'freelancer') {
            $redirectRoute = 'freelancer.dashboard';
        } else {
            $redirectRoute = 'dashboard';
        }

        return redirect()->route($redirectRoute)->with('status', 'Password changed successfully.');
    }
}
