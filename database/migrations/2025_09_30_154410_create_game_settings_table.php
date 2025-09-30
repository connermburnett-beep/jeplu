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
        Schema::create('game_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('game_id')->constrained()->onDelete('cascade');
            $table->string('background_image')->nullable();
            $table->string('theme')->default('classic'); // classic, dark, light, custom
            $table->string('background_music')->nullable();
            $table->string('buzzer_sound')->nullable();
            $table->integer('buzzer_timer')->default(5); // seconds to answer after buzzing
            $table->json('custom_colors')->nullable(); // For custom theme colors
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('game_settings');
    }
};
