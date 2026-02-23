<?php

// database/migrations/create_journal_entry_lines_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('journal_entry_lines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('journal_entry_id')->constrained()->onDelete('cascade');
            $table->foreignId('chart_of_account_id')->constrained()->onDelete('restrict');
            $table->text('description')->nullable();
            $table->decimal('debit', 15, 2)->default(0);
            $table->decimal('credit', 15, 2)->default(0);
            $table->string('vat_code')->nullable();
            $table->decimal('vat_amount', 15, 2)->default(0);
            $table->string('cost_center')->nullable();
            $table->string('profit_center')->nullable();
            $table->integer('line_order')->default(0);
            $table->timestamps();

            $table->index(['journal_entry_id', 'line_order']);
            $table->index('chart_of_account_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('journal_entry_lines');
    }
};
