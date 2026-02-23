<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('trial_balance_snapshots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->date('as_of_date');
            $table->json('balances'); // Store account balances as JSON
            $table->decimal('total_debits', 15, 2);
            $table->decimal('total_credits', 15, 2);
            $table->boolean('is_balanced')->default(false);
            $table->foreignId('generated_by')->constrained('users');
            $table->timestamps();

            $table->index(['company_id', 'as_of_date']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('trial_balance_snapshots');
    }
};
