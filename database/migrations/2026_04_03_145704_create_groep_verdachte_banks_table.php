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
        Schema::create('groep_verdachte_banks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('groep_id')->constrained('groeps')->cascadeOnDelete();
            $table->integer('verdachte_nummer');
            $table->integer('banked_amount')->default(0);
            $table->integer('confiscated_amount')->default(0);
            $table->timestamp('last_banked_at')->nullable();
            $table->timestamp('confiscated_at')->nullable();
            $table->timestamps();

            $table->unique(['groep_id', 'verdachte_nummer']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('groep_verdachte_banks');
    }
};
