<?php

namespace App\Http\Controllers;

use App\Mail\SendInitialPassword;
use App\Models\Company;
use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class UserController extends Controller
{
    use AuthorizesRequests;

    public function index(Request $request)
    {
        $this->authorize('viewAny', User::class);

        $filters = $request->only('search');

        if (auth()->user()->role === 'admin') {
            $droles = ['other', 'admin'];
            $users = User::query()
                ->when($filters['search'] ?? null, function ($query, $search) {
                    $query->where('name', 'like', "%$search%")
                        ->orWhere('email', 'like', "%$search%");
                })
                // ->where('company', '=', auth()->user()->company)
                ->orderBy('id', 'asc')
                ->paginate(100)
                ->withQueryString();
        } else {
            $droles = ['freelancer', 'company', 'admin', 'other'];
            $users = User::query()
                ->when($filters['search'] ?? null, function ($query, $search) {
                    $query->where('name', 'like', "%$search%")
                        ->orWhere('email', 'like', "%$search%");
                })
                ->orderBy('id', 'asc')
                ->paginate(100)
                ->withQueryString();
        }

        return Inertia::render('Settings/UserManagement', [
            'users' => $users,
            'filters' => $filters,
            'roles' => $droles,
            'auth' => [
                'user' => auth()->user(),
            ],
        ]);
    }

    private function generateRandomCompany(int $length): string
    {
        $bytes = random_bytes(ceil($length / 2));

        return substr(bin2hex($bytes), 0, $length);
    }

    private function generateRandomString(int $length): string
    {
        return substr(str_shuffle(
            str_repeat('!@#$%^&*()_+-=abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789', 5)
        ), 0, $length);
    }

    public function updatePermissions(Request $request, User $user)
    {
        $this->authorize('update', $user);

        $data = $request->validate([
            'menu_permissions' => 'array',
            'menu_permissions.*' => 'string',
            'permissions' => 'array',
            'permissions.*' => 'integer',
        ]);

        // Update menu permissions
        $user->menu_permissions = $data['menu_permissions'] ?? [];
        $user->save();

        // Update additional permissions if needed
        // This would depend on how you want to handle the additional permissions
        // For now, we're just handling menu permissions as requested

        return back()->with('success', 'Permissions updated.');
    }

    public function sendResetLink(User $user)
    {
        $this->authorize('update', $user);
        Password::sendResetLink(['email' => $user->email]);

        return back()->with('success', 'Reset link sent to '.$user->email);
    }

    public function store(Request $request)
    {
        $this->authorize('create', User::class);

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'role' => ['required', Rule::in(['freelancer', 'admin', 'other', 'company'])],
            'role_alias' => 'nullable|string|max:255',
        ]);

        // Generate a random password
        $password = $this->generateRandomString(16);

        // Create the user
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'role' => $data['role'],
            'role_alias' => $data['role_alias'] ?? null,
            'must_change_password' => false,
            'password' => bcrypt($password), // temporary password, will be changed
        ]);

        // If the user is a company, create a company entry for them
        if ($data['role'] === 'company') {
            $company = Company::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'is_active' => true,
            ]);

            // Update the user with the company_id
            $user->update(['company_id' => $company->id]);
        }

        // Send initial password to user
        Mail::to($user->email)->send(new SendInitialPassword($user, $password));

        return back()->with('success', 'User created and initial password sent.');
    }

    public function update(Request $request, User $user)
    {
        $this->authorize('update', $user);

        $request->validate([
            'role' => ['required', Rule::in(['freelancer', 'admin', 'other', 'company'])],
        ]);

        $user->role = $request->role;
        if (isset($request->name)) {
            $user->name = $request->name;
        }
        if (isset($request->email)) {
            $user->name = $request->email;
        }
        if (isset($request->role_alias)) {
            $user->role_alias = $request->role_alias;
        }
        $user->is_active = $request->is_active;

        $user->save();

        return back()->with('success', 'User updated.');
    }

    public function destroy(User $user)
    {
        $this->authorize('delete', $user);

        $user->delete();

        return back()->with('success', 'User deleted.');
    }
}
