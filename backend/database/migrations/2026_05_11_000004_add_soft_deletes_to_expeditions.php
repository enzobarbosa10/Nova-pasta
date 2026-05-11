<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * [ALTO 4] Adds soft-delete support to expeditions.
 *
 * Adds the `deleted_at` nullable timestamp column required by Laravel's
 * SoftDeletes trait on the Expedition model.
 *
 * Soft-deleted expeditions are invisible to all standard queries but
 * can be restored with Expedition::withTrashed()->find($id)->restore().
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('expeditions', function (Blueprint $table) {
            if (! Schema::hasColumn('expeditions', 'deleted_at')) {
                $table->softDeletes(); // adds nullable deleted_at column
            }
        });
    }

    public function down(): void
    {
        Schema::table('expeditions', function (Blueprint $table) {
            if (Schema::hasColumn('expeditions', 'deleted_at')) {
                $table->dropSoftDeletes();
            }
        });
    }
};
