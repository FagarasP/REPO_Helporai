<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Team;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TeamController extends Controller
{
    public function index(Request $request)
    {
        // Ensure user is authenticated
        if (! auth()->check()) {
            abort(401);
        }

        $teams = Team::whereHas('project', function ($query) {
            $query->where('company_id', auth()->user()->company_id);
        })
            ->with(['project', 'users'])
            ->get();

        return Inertia::render('Company/Teams/Index', [
            'teams' => $teams,
        ]);
    }

    public function create(Request $request)
    {
        $projects = Project::where('company_id', auth()->user()->company_id)
            ->where('status', 'published')
            ->get();

        // Get freelancers who applied to projects (all by default)
        $freelancers = collect();
        if ($request->project_id) {
            // If a project is selected, get only freelancers who applied to that project
            $freelancers = User::where('role', 'freelancer')
                ->whereHas('projectApplications', function ($query) use ($request) {
                    $query->where('project_id', $request->project_id);
                })
                ->get();
        } else {
            // If no project is selected, get all freelancers who applied to any project of this company
            $freelancers = User::where('role', 'freelancer')
                ->whereHas('projectApplications', function ($query) {
                    $query->whereHas('project', function ($subQuery) {
                        $subQuery->where('company_id', auth()->user()->company_id);
                    });
                })
                ->get();
        }

        return Inertia::render('Company/Teams/Create', [
            'projects' => $projects,
            'freelancers' => $freelancers,
            'project_id' => $request->project_id,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'project_id' => 'required|exists:projects,id',
            'description' => 'nullable|string',
            'internal_notes' => 'nullable|string',
            'freelancers' => 'nullable|array',
        ]);

        $team = Team::create([
            'name' => $request->name,
            'project_id' => $request->project_id,
            'description' => $request->description,
            'internal_notes' => $request->internal_notes,
        ]);

        if ($request->has('freelancers')) {
            $team->users()->attach($request->freelancers);
        }

        return redirect()->route('company.projects.index');
    }

    public function edit(Team $team)
    {
        $projects = Project::where('company_id', auth()->user()->company_id)
            ->where('status', 'published')
            ->get();

        // Get freelancers who applied to the team's project
        $freelancers = User::where('role', 'freelancer')
            ->whereHas('projectApplications', function ($query) use ($team) {
                $query->where('project_id', $team->project_id);
            })
            ->get();

        $team->load('users');

        return Inertia::render('Company/Teams/Edit', [
            'team' => $team,
            'projects' => $projects,
            'freelancers' => $freelancers,
        ]);
    }

    public function update(Request $request, Team $team)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'project_id' => 'required|exists:projects,id',
            'description' => 'nullable|string',
            'internal_notes' => 'nullable|string',
            'freelancers' => 'nullable|array',
        ]);

        $team->update([
            'name' => $request->name,
            'project_id' => $request->project_id,
            'description' => $request->description,
            'internal_notes' => $request->internal_notes,
        ]);

        if ($request->has('freelancers')) {
            $team->users()->sync($request->freelancers);
        }

        return redirect()->route('company.projects.index');
    }
}
