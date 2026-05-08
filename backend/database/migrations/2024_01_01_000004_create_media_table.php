<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('media', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('url');
            $table->enum('type', ['PHOTO', 'VIDEO', 'DRONE', 'REEL', 'STORY', 'REVIEW']);
            $table->uuid('expedition_id')->nullable();
            $table->json('tags')->nullable();
            $table->timestamps();
            
            $table->index('expedition_id');
            $table->index('type');
            
            $table->foreign('expedition_id')
                  ->references('id')
                  ->on('expeditions')
                  ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('media');
    }
};
