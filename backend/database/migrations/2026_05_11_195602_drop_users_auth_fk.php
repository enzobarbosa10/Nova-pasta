<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Drop the Supabase auth FK so Laravel can manage users independently.
        DB::statement('ALTER TABLE users DROP CONSTRAINT IF EXISTS users_id_fkey');
        // Add default UUID generation so new inserts don't need an id passed in.
        DB::statement('ALTER TABLE users ALTER COLUMN id SET DEFAULT gen_random_uuid()');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE users ALTER COLUMN id DROP DEFAULT');
        DB::statement('ALTER TABLE users ADD CONSTRAINT users_id_fkey FOREIGN KEY (id) REFERENCES auth.users(id) ON DELETE CASCADE');
    }
};
