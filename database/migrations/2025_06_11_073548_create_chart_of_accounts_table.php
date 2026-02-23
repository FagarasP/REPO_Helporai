<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('chart_of_accounts', function (Blueprint $table) {
            $table->id();
            $table->string('account_number')->unique();
            $table->string('account_name');
            $table->enum('account_type', ['asset', 'liability', 'equity', 'revenue', 'expense']);
            $table->string('account_sub_type')->nullable();
            $table->text('description')->nullable();
            $table->decimal('tax_rate', 5, 2)->nullable();
            $table->decimal('opening_balance', 15, 2)->default(0);
            $table->decimal('current_balance', 15, 2)->default(0);
            $table->boolean('is_active')->default(true);
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->unsignedBigInteger('company_id')->nullable(); // Made nullable temporarily
            $table->timestamps();
            $table->softDeletes();

            // Add foreign key constraints only if the referenced tables exist
            if (Schema::hasTable('chart_of_accounts')) {
                $table->foreign('parent_id')->references('id')->on('chart_of_accounts')->onDelete('set null');
            }

            // We'll add the company foreign key constraint in a separate migration
            $table->index(['company_id', 'account_type']);
            $table->index(['company_id', 'is_active']);
            $table->index(['account_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chart_of_accounts');
    }
};
