<?php

namespace App\Http\Controllers;

use App\Models\ChatMessage;
use App\Models\ChatRoom;
use App\Models\Company;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CommunicationController extends Controller
{
    public function index(Request $request): Response
    {
        $user = $request->user();

        if (! in_array($user->role, ['admin', 'company'])) {
            abort(403);
        }

        $rooms = $this->getAccessibleRooms($user->role, $user->company_id);

        if ($user->role === 'company') {
            $this->ensureCompanyTeamRoom($user->company_id, $user->id);
            $rooms = $this->getAccessibleRooms($user->role, $user->company_id);
        }

        $activeRoomId = (int) $request->integer('room_id');
        $activeRoom = $rooms->firstWhere('id', $activeRoomId) ?? $rooms->first();

        $messages = collect();
        if ($activeRoom) {
            $messages = ChatMessage::query()
                ->where('chat_room_id', $activeRoom->id)
                ->with('user:id,name,role')
                ->latest()
                ->take(100)
                ->get()
                ->reverse()
                ->values();
        }

        return Inertia::render('Communication/Index', [
            'rooms' => $rooms,
            'activeRoomId' => $activeRoom?->id,
            'messages' => $messages,
            'canManageAllRooms' => $user->role === 'admin',
        ]);
    }

    public function storeMessage(Request $request, ChatRoom $room): RedirectResponse
    {
        $user = $request->user();
        $this->authorizeRoomAccess($user->role, $user->company_id, $room);

        $data = $request->validate([
            'message' => 'required|string|max:3000',
        ]);

        ChatMessage::create([
            'chat_room_id' => $room->id,
            'user_id' => $user->id,
            'message' => $data['message'],
        ]);

        return back();
    }

    private function ensureCompanyTeamRoom(?int $companyId, int $creatorId): void
    {
        if (! $companyId) {
            return;
        }

        $company = Company::find($companyId);
        $companyName = $company?->name ?? "Company {$companyId}";

        ChatRoom::firstOrCreate(
            [
                'type' => 'company_team',
                'company_id' => $companyId,
            ],
            [
                'name' => "{$companyName} Team Chat",
                'created_by' => $creatorId,
            ]
        );
    }

    private function authorizeRoomAccess(string $role, ?int $companyId, ChatRoom $room): void
    {
        if ($role === 'admin') {
            return;
        }

        if ($role !== 'company' || ! $companyId || $room->company_id !== $companyId) {
            abort(403);
        }
    }

    private function getAccessibleRooms(string $role, ?int $companyId)
    {
        $query = ChatRoom::query()->with('company:id,name')->orderBy('name');

        if ($role === 'admin') {
            return $query->get();
        }

        return $query
            ->where('company_id', $companyId)
            ->get();
    }
}
