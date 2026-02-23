<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_presences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->enum('status', ['online', 'offline', 'away'])->default('offline');
            $table->timestamp('last_seen_at')->nullable();
            $table->timestamps();

            $table->unique('user_id');
            $table->index(['status', 'last_seen_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_presences');
    }
};
