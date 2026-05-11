<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Supabase created the table with full_name; add the columns Laravel auth needs
            if (! Schema::hasColumn('users', 'name')) {
                $table->string('name')->nullable()->after('email');
            }
            if (! Schema::hasColumn('users', 'password')) {
                $table->string('password')->nullable()->after('name');
            }
            if (! Schema::hasColumn('users', 'active')) {
                $table->boolean('active')->default(true)->after('role');
            }
            if (! Schema::hasColumn('users', 'last_login_at')) {
                $table->timestamp('last_login_at')->nullable();
            }
            if (! Schema::hasColumn('users', 'remember_token')) {
                $table->rememberToken();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumnIfExists('name');
            $table->dropColumnIfExists('password');
            $table->dropColumnIfExists('active');
            $table->dropColumnIfExists('last_login_at');
            $table->dropColumnIfExists('remember_token');
        });
    }
};
