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
        Schema::table('opdrachts', function (Blueprint $table) {
            $table->foreignId('big_boss_query_id')
                ->nullable()
                ->after('is_big_boss')
                ->constrained('big_boss_queries')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('opdrachts', function (Blueprint $table) {
            $table->dropConstrainedForeignId('big_boss_query_id');
        });
    }
};
