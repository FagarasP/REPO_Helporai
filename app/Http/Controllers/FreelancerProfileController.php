<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FreelancerProfileController extends Controller
{
    public function store(Request $request)
    {
        $user = Auth::user();

        if ($user->role !== 'freelancer') {
            abort(403, 'Only freelancer users can add profile details.');
        }

        $validated = $request->validate([
            'languages' => ['nullable', 'array'],
            'languages.*.language' => ['required', 'string', 'max:255'],
            'languages.*.level' => ['required', 'string', 'max:255'],
            'years_of_experience' => ['nullable', 'integer', 'min:0'],
            'availability' => ['nullable', 'string', 'max:255'],
            'preferred_shift' => ['nullable', 'string', 'max:255'],
            'specializations' => ['nullable', 'array'],
            'specializations.*' => ['string', 'max:255'],
            'certifications' => ['nullable', 'array'],
            'certifications.*' => ['string', 'max:255'],
            'bio' => ['nullable', 'string'],
        ]);

        $user->fill($validated);
        $user->save();

        return redirect()->back()->with('success', 'Freelancer profile added successfully.');
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        if ($user->role !== 'freelancer') {
            abort(403, 'Only freelancer users can update profile details.');
        }

        $validated = $request->validate([
            'languages' => ['nullable', 'array'],
            'languages.*.language' => ['required', 'string', 'max:255'],
            'languages.*.level' => ['required', 'string', 'max:255'],
            'years_of_experience' => ['nullable', 'integer', 'min:0'],
            'availability' => ['nullable', 'string', 'max:255'],
            'preferred_shift' => ['nullable', 'string', 'max:255'],
            'specializations' => ['nullable', 'array'],
            'specializations.*' => ['string', 'max:255'],
            'certifications' => ['nullable', 'array'],
            'certifications.*' => ['string', 'max:255'],
            'bio' => ['nullable', 'string'],
        ]);

        $user->fill($validated);
        $user->save();

        return redirect()->back()->with('success', 'Freelancer profile updated successfully.');
    }
}
