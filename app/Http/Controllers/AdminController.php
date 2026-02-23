<?php

namespace App\Http\Controllers;

use App\Models\ChatRoom;
use App\Models\Project;
use App\Models\Ticket;
use App\Models\User;
use Inertia\Inertia;
use Inertia\Response;

class AdminController extends Controller
{
    public function panel(): Response
    {
        $user = auth()->user();

        if (! $user || ! $user->isAdmin()) {
            abort(403);
        }

        return Inertia::render('Admin/Panel', [
            'stats' => [
                'users' => User::count(),
                'projects' => Project::count(),
                'openTickets' => Ticket::whereIn('status', ['open', 'in_progress'])->count(),
                'chatRooms' => class_exists(ChatRoom::class) ? ChatRoom::count() : 0,
            ],
            'quickLinks' => [
                ['label' => 'User Management', 'route' => 'settings.user-management', 'description' => 'Benutzer erstellen, bearbeiten und löschen'],
                ['label' => 'Project Settings', 'route' => 'settings.project-settings.index', 'description' => 'Projektwerte und Kataloge verwalten'],
                ['label' => 'All Projects', 'route' => 'other.project-management', 'description' => 'Projekte über alle Companies steuern'],
                ['label' => 'Tickets', 'route' => 'tickets.index', 'description' => 'Support-Tickets prüfen und bearbeiten'],
                ['label' => 'Communication Hub', 'route' => 'communication.index', 'description' => 'Team-Chats der Companies überwachen'],
            ],
        ]);
    }
}
