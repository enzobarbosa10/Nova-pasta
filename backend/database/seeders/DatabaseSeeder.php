<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * SECURITY: Passwords are randomly generated at seed time and written to
     * storage/app/seed-credentials.txt (gitignored). No credentials are
     * hardcoded in source code or committed to version control.
     */
    public function run(): void
    {
        $credentials = [];

        $masterEmail = env('MASTER_ADMIN_EMAIL');

        if (empty($masterEmail) || ! filter_var($masterEmail, FILTER_VALIDATE_EMAIL)) {
            $this->command->error('MASTER_ADMIN_EMAIL não definido ou inválido no .env — abortando seed.');
            return;
        }

        $users = [
            ['name' => 'Master Admin',  'email' => $masterEmail,                'role' => User::ROLE_MASTER_ADMIN],
            ['name' => 'Administrador', 'email' => env('ADMIN_EMAIL',    'admin@expedition.com'),    'role' => User::ROLE_ADMIN],
            ['name' => 'Operador',      'email' => env('OPERATOR_EMAIL', 'operator@expedition.com'), 'role' => User::ROLE_OPERATOR],
            ['name' => 'Guia',          'email' => env('GUIDE_EMAIL',    'guide@expedition.com'),    'role' => User::ROLE_OPERATOR],
        ];

        foreach ($users as $userData) {
            $password = Str::random(16);

            User::updateOrCreate(
                ['email' => $userData['email']],
                [
                    'name'     => $userData['name'],
                    'password' => Hash::make($password),
                    'role'     => $userData['role'],
                    'active'   => true,
                ]
            );

            $credentials[] = sprintf(
                "%s (%s)\n  Email:    %s\n  Password: %s",
                $userData['name'],
                $userData['role'],
                $userData['email'],
                $password
            );
        }

        // Write credentials to a gitignored file — delete after first use in production.
        $contents  = "=== Seed Credentials — generated at " . now()->toDateTimeString() . " ===\n\n";
        $contents .= implode("\n\n", $credentials) . "\n\n";
        $contents .= "DELETE this file from the server after recording the passwords.\n";

        Storage::disk('local')->put('seed-credentials.txt', $contents);

        $this->command->info('Seed credentials saved to storage/app/seed-credentials.txt');
        $this->command->warn('DELETE the credentials file from the server after recording the passwords!');
    }
}
