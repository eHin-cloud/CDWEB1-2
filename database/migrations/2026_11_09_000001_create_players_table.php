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
        Schema::create('players', function (Blueprint $table) {
            $table->id();
            $table->string('name')->index();
            $table->string('full_name');
            $table->string('nickname')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->string('birth_place')->nullable();
            $table->string('nationality')->default('Argentina')->index();
            $table->string('position')->nullable();
            $table->unsignedInteger('jersey_number')->default(10);
            $table->string('current_club')->nullable()->index();
            $table->json('former_clubs')->nullable();
            $table->string('height')->nullable();
            $table->string('preferred_foot')->nullable();
            $table->text('biography')->nullable();
            $table->json('career_stats')->nullable();
            $table->json('achievements')->nullable();
            $table->json('famous_matches')->nullable();
            $table->string('avatar_url')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('players');
    }
};

