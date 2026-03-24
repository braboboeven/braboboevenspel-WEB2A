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
        Schema::create('opdrachts', function (Blueprint $table) {
            $table->id();
            $table->string('code', 20)->unique();
            $table->string('titel')->nullable();
            $table->text('prompt');
            $table->text('correct_query');
            $table->string('source_table', 20)->default('Verdachte');
            $table->integer('verdachte_nummer')->nullable();
            $table->integer('step_nummer')->nullable();
            $table->boolean('is_big_boss')->default(false);
            $table->integer('reward_correct')->default(1000);
            $table->integer('reward_bad_format')->default(500);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('opdrachts');
    }
};
