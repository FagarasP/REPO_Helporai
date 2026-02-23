<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('conversations', function (Blueprint $table) {
            $table->id();
            $table->string('type')->default('direct');
            $table->string('title')->nullable();
            $table->timestamps();
        });

        Schema::create('conversation_participants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('conversation_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->timestamp('last_read_at')->nullable();
            $table->timestamps();

            $table->unique(['conversation_id', 'user_id']);
            $table->index(['user_id', 'conversation_id']);
        });

        Schema::create('conversation_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('conversation_id')->constrained()->cascadeOnDelete();
            $table->foreignId('sender_id')->constrained('users')->cascadeOnDelete();
            $table->text('body');
            $table->string('attachment_path')->nullable();
            $table->unsignedInteger('attachment_size')->nullable();
            $table->timestamps();

            $table->index(['conversation_id', 'created_at']);
        });

        Schema::create('conversation_call_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('conversation_id')->constrained()->cascadeOnDelete();
            $table->foreignId('initiator_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('receiver_id')->constrained('users')->cascadeOnDelete();
            $table->enum('call_type', ['audio', 'video'])->default('video');
            $table->enum('status', ['started', 'ended', 'missed', 'rejected'])->default('started');
            $table->timestamp('started_at')->nullable();
            $table->timestamp('ended_at')->nullable();
            $table->timestamps();

            $table->index(['conversation_id', 'started_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('conversation_call_logs');
        Schema::dropIfExists('conversation_messages');
        Schema::dropIfExists('conversation_participants');
        Schema::dropIfExists('conversations');
    }
};
