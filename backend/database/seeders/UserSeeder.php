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
        // Create admin user
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@expedition.com',
            'password' => Hash::make('password'),
            'role' => 'ADMIN',
        ]);

        // Create operator user
        User::create([
            'name' => 'Operator User',
            'email' => 'operator@expedition.com',
            'password' => Hash::make('password'),
            'role' => 'OPERATOR',
        ]);

        // Create guide user
        User::create([
            'name' => 'Guide User',
            'email' => 'guide@expedition.com',
            'password' => Hash::make('password'),
            'role' => 'GUIDE',
        ]);
    }
}
