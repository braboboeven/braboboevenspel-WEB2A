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
        Schema::create('hint_verzendings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('groep_id')->nullable()->constrained('groeps')->cascadeOnDelete();
            $table->unsignedInteger('hint_nummer')->nullable();
            $table->foreignId('big_boss_hint_id')->nullable()->constrained('big_boss_hints')->nullOnDelete();
            $table->foreignId('sent_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('sent_at')->useCurrent();
            $table->timestamps();

            $table->foreign('hint_nummer')->references('hint_nummer')->on('hints')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hint_verzendings');
    }
};
