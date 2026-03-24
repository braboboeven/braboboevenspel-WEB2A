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
        Schema::create('pogings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('groep_id')->constrained('groeps')->cascadeOnDelete();
            $table->foreignId('opdracht_id')->constrained('opdrachts')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('submitted_query');
            $table->boolean('is_correct')->default(false);
            $table->boolean('is_good_format')->default(false);
            $table->integer('earned')->default(0);
            $table->timestamp('submitted_at')->useCurrent();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pogings');
    }
};
