<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * [ALTO 3] Creates the lead_notes table.
 *
 * Replaces the legacy "notes" text field on the leads table, providing:
 *   - Individual note records with full audit trail
 *   - Author attribution (user_id)
 *   - Edit and delete support per note
 *   - created_at / updated_at timestamps
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lead_notes', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('lead_id')
                  ->constrained('leads')
                  ->cascadeOnDelete();
            $table->foreignUuid('user_id')
                  ->nullable()   // nullable so migrated notes without a known author are preserved
                  ->constrained('users')
                  ->nullOnDelete();
            $table->text('body');
            $table->timestamps();

            $table->index('lead_id');
            $table->index(['lead_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lead_notes');
    }
};
