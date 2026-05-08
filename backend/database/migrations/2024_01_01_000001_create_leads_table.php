<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('leads', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('whatsapp');
            $table->string('instagram')->nullable();
            $table->string('source');
            $table->string('interest');
            $table->string('destination');
            $table->date('date_desired');
            $table->integer('people_count');
            $table->decimal('estimated_ticket', 10, 2);
            $table->enum('status', [
                'NEW', 'CONTACTED', 'QUALIFIED', 'PROPOSAL', 
                'RESERVED', 'PAID', 'POST_TRIP', 'REFERRAL'
            ])->default('NEW');
            $table->text('notes')->nullable();
            $table->date('last_contact');
            $table->date('next_follow_up');
            $table->json('tags')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->index('status');
            $table->index('next_follow_up');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('leads');
    }
};
