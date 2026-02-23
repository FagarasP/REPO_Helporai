<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('legal_name')->nullable();
            $table->text('address')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('postal_code')->nullable();
            $table->string('country')->default('DE');
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('website')->nullable();
            $table->string('tax_number')->nullable();
            $table->string('vat_id')->nullable();
            $table->string('commercial_register_number')->nullable();
            $table->string('currency', 3)->default('EUR');
            // Option 1: Use nullable dates without defaults
            $table->date('financial_year_start')->nullable();
            $table->date('financial_year_end')->nullable();

            // Option 2: Or use string fields for month-day format
            // $table->string('financial_year_start', 5)->default('01-01'); // MM-DD format
            // $table->string('financial_year_end', 5)->default('12-31');   // MM-DD format

            $table->string('logo_path')->nullable();
            $table->boolean('is_active')->default(true);
            $table->json('settings')->nullable();
            $table->timestamps();

            $table->index(['is_active']);
            $table->index(['country']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};
