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
        Schema::table('spel_sessies', function (Blueprint $table) {
            $table->string('winner_group_name')->nullable()->after('created_by_user_id');
            $table->integer('winner_total_score')->nullable()->after('winner_group_name');
            $table->timestamp('winner_declared_at')->nullable()->after('winner_total_score');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('spel_sessies', function (Blueprint $table) {
            $table->dropColumn([
                'winner_group_name',
                'winner_total_score',
                'winner_declared_at',
            ]);
        });
    }
};
