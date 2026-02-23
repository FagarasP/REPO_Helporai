<?php

namespace App\Http\Controllers;

use App\Models\ProjectSetting;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProjectSettingController extends Controller
{
    public function index()
    {
        $settings = ProjectSetting::all();

        return Inertia::render('Settings/ProjectSettings/Index', [
            'settings' => $settings,
        ]);
    }

    public function create()
    {
        return Inertia::render('Settings/ProjectSettings/Create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'value' => 'required|string|max:255',
            'type' => 'required|string|in:project_language,job_type,payment_offer',
        ]);

        ProjectSetting::create([
            'key' => $request->value,
            'value' => $request->value,
            'type' => $request->type,
        ]);

        return redirect()->route('settings.project-settings.index');
    }

    public function edit(ProjectSetting $projectSetting)
    {
        return Inertia::render('Settings/ProjectSettings/Edit', [
            'setting' => $projectSetting,
        ]);
    }

    public function update(Request $request, ProjectSetting $projectSetting)
    {
        $request->validate([
            'value' => 'required|string|max:255',
            'type' => 'required|string|in:project_language,job_type,payment_offer',
        ]);

        $projectSetting->update([
            'key' => $request->value,
            'value' => $request->value,
            'type' => $request->type,
        ]);

        return redirect()->route('settings.project-settings.index');
    }

    public function destroy(ProjectSetting $projectSetting)
    {
        $projectSetting->delete();

        return redirect()->route('settings.project-settings.index');
    }
}
