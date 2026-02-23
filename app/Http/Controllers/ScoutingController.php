<?php

namespace App\Http\Controllers;

use App\Http\Resources\FreelancerResource;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ScoutingController extends Controller
{
    public function index(Request $request)
    {
        $freelancers = User::where('role', 'freelancer')->get();
        $uniqueLanguages = User::where('role', 'freelancer')
            ->whereNotNull('languages')
            ->get()
            ->flatMap(function ($user) {
                return $user->languages ? collect($user->languages)->pluck('language') : [];
            })
            ->unique()
            ->sort()
            ->values()
            ->all();

        return Inertia::render('Company/Workforce/Scounting', [
            'freelancers' => FreelancerResource::collection($freelancers),
            'uniqueLanguages' => $uniqueLanguages,
        ]);
    }

    public function search(Request $request)
    {
        $query = User::where('role', 'freelancer');

        if ($request->has('languages')) {
            $query->whereIn('language', $request->input('languages'));
        }

        if ($request->has('experience')) {
            $query->where('experience', '>=', $request->input('experience'));
        }

        if ($request->has('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->input('search') . '%')
                    ->orWhere('email', 'like', '%' . $request->input('search') . '%');
            });
        }

        $freelancers = $query->get();

        return FreelancerResource::collection($freelancers);
    }
}
