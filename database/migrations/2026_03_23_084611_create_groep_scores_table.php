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
        Schema::create('groep_scores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('groep_id')->constrained('groeps')->cascadeOnDelete();
            $table->integer('score')->default(0);
            $table->integer('big_boss_score')->default(0);
            $table->timestamp('last_submission_at')->nullable();
            $table->timestamps();

            $table->unique('groep_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('groep_scores');
    }
};
