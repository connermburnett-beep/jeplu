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
            // Cashier already adds stripe_id, pm_type, pm_last_four, trial_ends_at
            // We only need to add our custom fields
            $table->enum('subscription_tier', ['free', 'basic', 'premium'])->default('free');
            $table->integer('ai_questions_used')->default(0);
            $table->timestamp('ai_questions_reset_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'subscription_tier',
                'ai_questions_used',
                'ai_questions_reset_at',
            ]);
        });
    }
};
