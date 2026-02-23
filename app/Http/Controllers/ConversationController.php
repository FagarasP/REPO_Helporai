<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\ConversationMessage;
use App\Models\ConversationParticipant;
use App\Models\User;
use App\Services\CommunicationAuthorizationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class ConversationController extends Controller
{
    public function __construct(private readonly CommunicationAuthorizationService $authorization)
    {
    }

    public function index(Request $request)
    {
        $user = $request->user();

        $conversations = Conversation::query()
            ->whereHas('participants', fn ($q) => $q->where('user_id', $user->id))
            ->with([
                'participants.user:id,name,role,company_id',
                'messages' => fn ($q) => $q->latest()->limit(1)->with('sender:id,name'),
            ])
            ->latest('updated_at')
            ->get()
            ->map(function (Conversation $conversation) use ($user) {
                $lastMessage = $conversation->messages->first();
                $unreadCount = ConversationMessage::query()
                    ->where('conversation_id', $conversation->id)
                    ->where('sender_id', '!=', $user->id)
                    ->where('created_at', '>', optional($conversation->participants->firstWhere('user_id', $user->id))->last_read_at)
                    ->count();

                return [
                    'id' => $conversation->id,
                    'title' => $this->conversationTitle($conversation, $user->id),
                    'last_message' => $lastMessage?->body,
                    'last_message_at' => $lastMessage?->created_at,
                    'unread_count' => $unreadCount,
                ];
            });

        return Inertia::render('Messages/Index', [
            'conversations' => $conversations,
        ]);
    }

    public function show(Conversation $conversation, Request $request)
    {
        $this->ensureParticipant($conversation, $request->user()->id);

        $conversation->load('participants.user:id,name,role');

        $messages = ConversationMessage::query()
            ->where('conversation_id', $conversation->id)
            ->with('sender:id,name,role')
            ->latest()
            ->limit(100)
            ->get()
            ->reverse()
            ->values();

        return Inertia::render('Messages/Show', [
            'conversation' => [
                'id' => $conversation->id,
                'title' => $this->conversationTitle($conversation, $request->user()->id),
                'participants' => $conversation->participants,
            ],
            'messages' => $messages,
            'turn' => [
                'urls' => [
                    'url' => env('TURN_URL'),
                    'username' => env('TURN_USER'),
                    'credential' => env('TURN_PASS'),
                ],
            ],
            'maxAttachmentMb' => (int) env('CHAT_MAX_ATTACHMENT_MB', 10),
        ]);
    }


    public function contacts(Request $request)
    {
        $user = $request->user();
        $search = (string) $request->query('q', '');

        $candidates = User::query()
            ->where('id', '!=', $user->id)
            ->when($search !== '', fn ($q) => $q->where('name', 'like', "%{$search}%"))
            ->limit(30)
            ->get(['id', 'name', 'role', 'company_id']);

        $allowed = $candidates->filter(fn (User $candidate) => $this->authorization->canUsersCommunicate($user, $candidate))->values();

        return response()->json(['data' => $allowed]);
    }

    public function createConversation(Request $request)
    {
        $data = $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $me = $request->user();
        $other = User::findOrFail($data['user_id']);

        if (! $this->authorization->canUsersCommunicate($me, $other)) {
            abort(403, 'You are not allowed to start a conversation with this user.');
        }

        $existingConversationId = Conversation::query()
            ->where('type', 'direct')
            ->whereHas('participants', fn ($q) => $q->where('user_id', $me->id))
            ->whereHas('participants', fn ($q) => $q->where('user_id', $other->id))
            ->withCount('participants')
            ->having('participants_count', 2)
            ->value('id');

        if ($existingConversationId) {
            return redirect()->route('messages.show', $existingConversationId);
        }

        $conversation = DB::transaction(function () use ($me, $other) {
            $conversation = Conversation::create([
                'type' => 'direct',
            ]);

            ConversationParticipant::create(['conversation_id' => $conversation->id, 'user_id' => $me->id]);
            ConversationParticipant::create(['conversation_id' => $conversation->id, 'user_id' => $other->id]);

            return $conversation;
        });

        return redirect()->route('messages.show', $conversation);
    }

    public function sendMessage(Conversation $conversation, Request $request)
    {
        $user = $request->user();
        $this->ensureParticipant($conversation, $user->id);

        $data = $request->validate([
            'body' => 'required_without:attachment|string|max:5000',
            'attachment' => 'nullable|file|max:'.(((int) env('CHAT_MAX_ATTACHMENT_MB', 10)) * 1024),
        ]);

        $attachmentPath = null;
        $attachmentSize = null;

        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('communications', env('CHAT_ATTACHMENT_DISK', 'public'));
            $attachmentSize = $request->file('attachment')->getSize();
        }

        ConversationMessage::create([
            'conversation_id' => $conversation->id,
            'sender_id' => $user->id,
            'body' => (string) ($data['body'] ?? ''),
            'attachment_path' => $attachmentPath,
            'attachment_size' => $attachmentSize,
        ]);

        $conversation->touch();

        return back();
    }

    public function listMessages(Conversation $conversation, Request $request)
    {
        $this->ensureParticipant($conversation, $request->user()->id);

        return response()->json([
            'data' => ConversationMessage::query()
                ->where('conversation_id', $conversation->id)
                ->with('sender:id,name,role')
                ->latest()
                ->limit(100)
                ->get()
                ->reverse()
                ->values(),
        ]);
    }


    public function typing(Conversation $conversation, Request $request)
    {
        $this->ensureParticipant($conversation, $request->user()->id);

        $data = $request->validate([
            'is_typing' => 'required|boolean',
        ]);

        return response()->json([
            'event' => 'message.typing',
            'conversation_id' => $conversation->id,
            'user_id' => $request->user()->id,
            'is_typing' => (bool) $data['is_typing'],
        ]);
    }

    public function markRead(Conversation $conversation, Request $request)
    {
        $this->ensureParticipant($conversation, $request->user()->id);

        ConversationParticipant::query()
            ->where('conversation_id', $conversation->id)
            ->where('user_id', $request->user()->id)
            ->update(['last_read_at' => now()]);

        return back();
    }

    public function attachment(string $disk, string $path)
    {
        if (! Storage::disk($disk)->exists($path)) {
            abort(404);
        }

        return Storage::disk($disk)->response($path);
    }

    private function ensureParticipant(Conversation $conversation, int $userId): void
    {
        if (! $conversation->participants()->where('user_id', $userId)->exists()) {
            abort(403, 'You are not a participant of this conversation.');
        }
    }

    private function conversationTitle(Conversation $conversation, int $authUserId): string
    {
        $otherParticipant = $conversation->participants->first(fn ($participant) => $participant->user_id !== $authUserId);

        return $otherParticipant?->user?->name ?? 'Conversation #'.$conversation->id;
    }
}
