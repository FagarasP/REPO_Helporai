<?php

use App\Models\User;

it('allows super admin to access admin panel', function () {
    $superAdmin = User::factory()->create([
        'role' => 'super_admin',
        'email' => 'admin@helpora.net',
        'email_verified_at' => now(),
    ]);

    $this->actingAs($superAdmin)
        ->get(route('admin.panel'))
        ->assertOk();
});
