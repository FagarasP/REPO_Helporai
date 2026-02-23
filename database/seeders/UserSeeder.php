<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Admin User',
            'email' => 'a@a.com',
            'password' => Hash::make('iulian'),
            'role' => 'admin',
            'is_active' => true,
        ]);

        User::create([
            'name' => 'Freelancer User',
            'email' => 'b@b.com',
            'password' => Hash::make('iulian'),
            'role' => 'freelancer',
            'is_active' => true,
        ]);

        User::create([
            'name' => 'Company User',
            'email' => 'c@c.com',
            'password' => Hash::make('iulian'),
            'role' => 'company',
            'is_active' => true,
        ]);
    }
}
