<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\ProjectApplication;
use App\Models\ProjectSetting;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProjectController extends Controller
{
    public function index()
    {
        // Ensure user is authenticated
        if (! auth()->check()) {
            abort(401);
        }

        $projects = Project::where('company_id', auth()->user()->company_id)
            ->withCount('applications')
            ->get();

        return Inertia::render('Company/Projects/Index', [
            'projects' => $projects,
        ]);
    }

    public function create()
    {
        // Ensure user is authenticated
        if (! auth()->check()) {
            abort(401);
        }

        return Inertia::render('Company/Projects/Create', [
            'project_languages' => ProjectSetting::where('type', 'project_language')->get(),
            'job_types' => ProjectSetting::where('type', 'job_type')->get(),
            'payment_offers' => ProjectSetting::where('type', 'payment_offer')->get(),
        ]);
    }

    public function store(Request $request)
    {
        // Ensure user is authenticated
        if (! auth()->check()) {
            abort(401);
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'project_language' => 'required|string',
            'job_type' => 'required|string',
            'payment_offer' => 'required|string',
            'payment_amount' => 'required|numeric',
            'status' => 'required|string|in:published,not_published',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
        ]);

        $project = Project::create([
            'company_id' => auth()->user()->company_id,
            'title' => $request->title,
            'description' => $request->description,
            'project_language' => $request->project_language,
            'job_type' => $request->job_type,
            'payment_offer' => $request->payment_offer,
            'payment_amount' => $request->payment_amount,
            'status' => $request->status,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
        ]);

        return redirect()->route('company.teams.create', ['project_id' => $project->id]);
    }

    public function edit(Project $project)
    {
        // Ensure user is authenticated
        if (! auth()->check()) {
            abort(401);
        }

        return Inertia::render('Company/Projects/Edit', [
            'project' => $project,
            'project_languages' => ProjectSetting::where('type', 'project_language')->get(),
            'job_types' => ProjectSetting::where('type', 'job_type')->get(),
            'payment_offers' => ProjectSetting::where('type', 'payment_offer')->get(),
        ]);
    }

    public function update(Request $request, Project $project)
    {
        // Ensure user is authenticated
        if (! auth()->check()) {
            abort(401);
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'project_language' => 'required|string',
            'job_type' => 'required|string',
            'payment_offer' => 'required|string',
            'payment_amount' => 'required|numeric',
            'status' => 'required|string|in:published,not_published',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
        ]);

        $project->update($request->all());

        return redirect()->route('company.projects.index');
    }

    public function destroy(Project $project)
    {
        // Ensure user is authenticated
        if (! auth()->check()) {
            abort(401);
        }

        $project->delete();

        return redirect()->route('company.projects.index');
    }

    public function jobOffers()
    {
        // Ensure user is authenticated
        if (! auth()->check()) {
            abort(401);
        }

        $projects = Project::with('company')->where('status', 'published')->orderBy('created_at', 'desc')->get();

        return Inertia::render('Freelancer/JobOffers/Index', [
            'projects' => $projects,
        ]);
    }

    public function showJobOffer(Project $project)
    {
        // Ensure user is authenticated
        if (! auth()->check()) {
            abort(401);
        }

        // Load the company relationship
        $project->load('company');

        // Check if the user has already applied
        $hasApplied = false;
        if (auth()->user()->role === 'freelancer') {
            $existingApplication = ProjectApplication::where('project_id', $project->id)
                ->where('user_id', auth()->id())
                ->first();

            $hasApplied = $existingApplication ? true : false;
        }

        return Inertia::render('Freelancer/JobOffers/Show', [
            'project' => $project,
            'hasApplied' => $hasApplied,
        ]);
    }

    public function apply(Request $request, Project $project)
    {
        // Ensure user is authenticated
        if (! auth()->check()) {
            return redirect()->back()->with('error', 'You must be logged in to apply for projects.');
        }

        // Check if user is a freelancer
        if (auth()->user()->role !== 'freelancer') {
            return redirect()->back()->with('error', 'Only freelancers can apply for projects.');
        }

        // Check if user has already applied
        $existingApplication = ProjectApplication::where('project_id', $project->id)
            ->where('user_id', auth()->id())
            ->first();

        if ($existingApplication) {
            return redirect()->back()->with('error', 'You have already applied for this project.');
        }

        // Create application
        ProjectApplication::create([
            'project_id' => $project->id,
            'user_id' => auth()->id(),
        ]);

        return redirect()->back()->with('success', 'Application submitted successfully!');
    }

    public function showApplicants(Project $project)
    {
        // Ensure user is authenticated
        if (! auth()->check()) {
            abort(401);
        }

        // Ensure only the project owner can see applicants
        if (auth()->user()->company_id !== $project->company_id) {
            abort(403);
        }

        $applications = ProjectApplication::with('user')
            ->where('project_id', $project->id)
            ->get();

        return response()->json($applications);
    }

    public function rejectApplication(ProjectApplication $application)
    {
        // Ensure user is authenticated
        if (! auth()->check()) {
            abort(401);
        }

        // Ensure only the project owner can reject applications
        if (auth()->user()->company_id !== $application->project->company_id) {
            abort(403);
        }

        $application->status = 'rejected';
        $application->save();

        return response()->json(['message' => 'Application rejected successfully.']);
    }
}
