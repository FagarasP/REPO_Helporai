<?php

use App\Models\Conversation;
use App\Models\ConversationParticipant;
use App\Models\User;

it('blocks non participants from opening chat', function () {
    $owner = User::factory()->create();
    $other = User::factory()->create();
    $intruder = User::factory()->create();

    $conversation = Conversation::create(['type' => 'direct']);
    ConversationParticipant::create(['conversation_id' => $conversation->id, 'user_id' => $owner->id]);
    ConversationParticipant::create(['conversation_id' => $conversation->id, 'user_id' => $other->id]);

    $this->actingAs($intruder)
        ->get(route('messages.show', $conversation))
        ->assertStatus(403);
});
