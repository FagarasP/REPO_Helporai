<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\ConversationCallLog;
use Illuminate\Http\Request;

class CallSignalController extends Controller
{
    public function offer(Conversation $conversation, Request $request)
    {
        $this->ensureParticipant($conversation, $request->user()->id);

        $data = $request->validate([
            'to_user_id' => 'required|exists:users,id',
            'sdp' => 'required|array',
            'call_type' => 'required|in:audio,video',
        ]);

        $log = ConversationCallLog::create([
            'conversation_id' => $conversation->id,
            'initiator_id' => $request->user()->id,
            'receiver_id' => $data['to_user_id'],
            'call_type' => $data['call_type'],
            'status' => 'started',
            'started_at' => now(),
        ]);

        return response()->json([
            'event' => 'call.offer',
            'conversation_id' => $conversation->id,
            'log_id' => $log->id,
            'from_user_id' => $request->user()->id,
            'to_user_id' => (int) $data['to_user_id'],
            'sdp' => $data['sdp'],
        ]);
    }

    public function answer(Conversation $conversation, Request $request)
    {
        $this->ensureParticipant($conversation, $request->user()->id);

        $data = $request->validate([
            'log_id' => 'required|exists:conversation_call_logs,id',
            'sdp' => 'required|array',
        ]);

        return response()->json([
            'event' => 'call.answer',
            'conversation_id' => $conversation->id,
            'log_id' => (int) $data['log_id'],
            'from_user_id' => $request->user()->id,
            'sdp' => $data['sdp'],
        ]);
    }

    public function ice(Conversation $conversation, Request $request)
    {
        $this->ensureParticipant($conversation, $request->user()->id);

        $data = $request->validate([
            'log_id' => 'required|exists:conversation_call_logs,id',
            'candidate' => 'required|array',
        ]);

        return response()->json([
            'event' => 'call.ice',
            'conversation_id' => $conversation->id,
            'log_id' => (int) $data['log_id'],
            'from_user_id' => $request->user()->id,
            'candidate' => $data['candidate'],
        ]);
    }

    public function hangup(Conversation $conversation, Request $request)
    {
        $this->ensureParticipant($conversation, $request->user()->id);

        $data = $request->validate([
            'log_id' => 'required|exists:conversation_call_logs,id',
        ]);

        ConversationCallLog::where('id', $data['log_id'])->update([
            'status' => 'ended',
            'ended_at' => now(),
        ]);

        return response()->json([
            'event' => 'call.hangup',
            'conversation_id' => $conversation->id,
            'log_id' => (int) $data['log_id'],
            'from_user_id' => $request->user()->id,
        ]);
    }

    private function ensureParticipant(Conversation $conversation, int $userId): void
    {
        if (! $conversation->participants()->where('user_id', $userId)->exists()) {
            abort(403);
        }
    }
}
