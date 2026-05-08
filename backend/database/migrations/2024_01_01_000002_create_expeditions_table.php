<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('expeditions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('cover_image')->nullable();
            $table->string('destination');
            $table->string('dates');
            $table->integer('capacity');
            $table->integer('remaining_spots');
            $table->uuid('guide_id')->nullable();
            $table->string('accommodation');
            $table->string('transport');
            $table->enum('trail_level', ['EASY', 'MODERATE', 'HARD', 'CHALLENGING']);
            $table->enum('status', [
                'PLANNING', 'OPEN', 'GUARANTEED', 
                'IN_PROGRESS', 'COMPLETED', 'CANCELLED'
            ])->default('PLANNING');
            $table->decimal('costs', 10, 2);
            $table->decimal('margin_predicted', 5, 2);
            $table->decimal('margin_real', 5, 2)->nullable();
            $table->json('participants')->nullable(); // Array of traveler IDs
            $table->timestamps();
            $table->softDeletes();
            
            $table->index('status');
            $table->index('guide_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('expeditions');
    }
};
