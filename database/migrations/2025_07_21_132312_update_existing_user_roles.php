<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Map old roles to new roles
        $roleMapping = [
            'admin' => 'company',
            'general_user' => 'other',
            'employee' => 'other',
        ];

        foreach ($roleMapping as $oldRole => $newRole) {
            User::where('role', $oldRole)->update(['role' => $newRole]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reverse the role mapping if needed
        $reverseMapping = [
            'company' => 'admin',
            'other' => 'general_user',
        ];

        foreach ($reverseMapping as $newRole => $oldRole) {
            User::where('role', $newRole)->update(['role' => $oldRole]);
        }
    }
};
