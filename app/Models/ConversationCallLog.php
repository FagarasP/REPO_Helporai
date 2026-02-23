<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConversationCallLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'conversation_id',
        'initiator_id',
        'receiver_id',
        'call_type',
        'status',
        'started_at',
        'ended_at',
    ];

    protected function casts(): array
    {
        return [
            'started_at' => 'datetime',
            'ended_at' => 'datetime',
        ];
    }
}
