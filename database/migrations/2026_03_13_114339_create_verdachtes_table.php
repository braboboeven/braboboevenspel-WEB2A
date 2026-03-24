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
        Schema::create('Verdachte', function (Blueprint $table) {
            $table->integer('verdachte_id')->primary();
            $table->string('naam', 50);
            $table->string('geslacht', 5);
            $table->integer('leeftijd');
            $table->string('lengte', 9);
            $table->string('haarkleur', 5);
            $table->string('kleur_ogen', 5);
            $table->boolean('gezichtsbeharing');
            $table->boolean('tatoeages');
            $table->string('bril', 3);
            $table->boolean('littekens')->nullable();
            $table->string('schoenmaat', 9);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('Verdachte');
    }
};
