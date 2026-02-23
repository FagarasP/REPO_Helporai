<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Inertia\Inertia;
use Inertia\Response;

class RegisterController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): Response
    {
        return Inertia::render('Auth/Register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|lowercase|email|max:255|unique:'.User::class,
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => 'required|in:freelancer,company',
        ]);

        // If the user is a company, create a company entry for them
        $companyId = null;
        if ($request->role === 'company') {
            $company = Company::create([
                'name' => $request->name,
                'email' => $request->email,
                'is_active' => true,
            ]);

            $companyId = $company->id;
        }

        // Create the user
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'company_id' => $companyId,
        ]);

        event(new Registered($user));

        Auth::login($user);

        // Redirect users to their appropriate dashboards based on role
        if ($user->role === 'company') {
            return redirect(route('company.dashboard', absolute: false));
        } elseif ($user->role === 'freelancer') {
            return redirect(route('freelancer.dashboard', absolute: false));
        } else {
            return redirect(route('other.dashboard', absolute: false));
        }
    }
}
