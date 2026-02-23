<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TicketController extends Controller
{
    public function index()
    {
        $query = Ticket::query()->with('user:id,name,email,role');

        if (auth()->user()->role !== 'admin') {
            $query->where('user_id', auth()->id());
        }

        return Inertia::render('Tickets/Index', [
            'tickets' => $query->latest()->get(),
            'isAdmin' => auth()->user()->role === 'admin',
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'priority' => 'required|in:low,medium,high',
            'category' => 'required|string|max:100',
        ]);

        Ticket::create([
            ...$data,
            'user_id' => auth()->id(),
            'status' => 'open',
        ]);

        return back()->with('success', 'Ticket created successfully.');
    }

    public function update(Request $request, Ticket $ticket)
    {
        if (auth()->user()->role !== 'admin' && $ticket->user_id !== auth()->id()) {
            abort(403);
        }

        $data = $request->validate([
            'status' => 'required|in:open,in_progress,resolved,closed',
        ]);

        $ticket->status = $data['status'];
        $ticket->resolved_at = in_array($data['status'], ['resolved', 'closed']) ? now() : null;
        $ticket->save();

        return back()->with('success', 'Ticket updated successfully.');
    }

    public function destroy(Ticket $ticket)
    {
        if (auth()->user()->role !== 'admin' && $ticket->user_id !== auth()->id()) {
            abort(403);
        }

        $ticket->delete();

        return back()->with('success', 'Ticket deleted.');
    }
}
