<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\ProjectApplication;
use App\Models\User;
use Inertia\Inertia;

class FreelancerController extends Controller
{
    public function dashboard()
    {
        return Inertia::render('Freelancer/Dashboard');
    }

    public function jobOffers()
    {
        return Inertia::render('Freelancer/JobOffers');
    }

    public function myProjects()
    {
        return Inertia::render('Freelancer/MyProjects');
    }

    public function shifts()
    {
        return Inertia::render('Freelancer/Shifts');
    }

    public function finance()
    {
        return Inertia::render('Freelancer/Finance');
    }

    public function profile(User $user)
    {
        // Ensure the user is a freelancer
        if ($user->role !== 'freelancer') {
            abort(404);
        }

        // Get all projects the freelancer has applied to or been assigned to, excluding rejected applications
        $projects = Project::whereHas('applications', function ($query) use ($user) {
            $query->where('user_id', $user->id)
                ->where('status', '!=', 'rejected');
        })->with('company')->get();

        // Get comments from companies about this freelancer (this would be implemented based on your data model)
        $comments = []; // Placeholder for comments functionality

        return Inertia::render('Freelancer/Profile', [
            'freelancer' => $user,
            'projects' => $projects,
            'comments' => $comments,
            'rating' => 0, // Placeholder for rating functionality
        ]);
    }

    public function publicProfile(User $user)
    {
        // Ensure the user is a freelancer
        if ($user->role !== 'freelancer') {
            abort(404);
        }

        // Load necessary relationships for the public profile
        $user->load('company', 'teams.projects');

        // Filter out rejected projects from the teams relationship
        $projects = $user->teams->flatMap(function ($team) {
            return $team->projects->filter(function ($project) {
                // Assuming project has a status through ProjectApplication, or directly
                // This part might need adjustment based on how project status is linked to the project itself
                return true; // For now, include all projects from teams
            });
        });

        return Inertia::render('Freelancer/Profile', [
            'freelancer' => $user,
            'projects' => $projects,
            'comments' => [], // Public profile might not show comments
            'rating' => 0, // Public profile might not show rating
        ]);
    }
}
