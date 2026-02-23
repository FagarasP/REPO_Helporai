<?php

namespace App\Http\Controllers;

use App\Models\UserPresence;
use Illuminate\Http\Request;

class PresenceController extends Controller
{
    public function heartbeat(Request $request)
    {
        UserPresence::updateOrCreate(
            ['user_id' => $request->user()->id],
            [
                'status' => $request->input('status', 'online'),
                'last_seen_at' => now(),
            ]
        );

        return response()->json(['ok' => true]);
    }

    public function bulk(Request $request)
    {
        $ids = collect(explode(',', (string) $request->query('user_ids')))
            ->map(fn ($id) => (int) $id)
            ->filter();

        return response()->json([
            'data' => UserPresence::query()
                ->whereIn('user_id', $ids)
                ->get(['user_id', 'status', 'last_seen_at']),
        ]);
    }
}
