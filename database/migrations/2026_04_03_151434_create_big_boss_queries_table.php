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
        Schema::create('big_boss_queries', function (Blueprint $table) {
            $table->id();
            $table->string('code', 20)->unique();
            $table->string('titel')->nullable();
            $table->text('correct_query');
            $table->integer('reward_correct')->default(10000);
            $table->integer('bad_format_penalty')->default(500);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('big_boss_queries');
    }
};
