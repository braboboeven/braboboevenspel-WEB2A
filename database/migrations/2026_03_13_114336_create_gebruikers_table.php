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
        Schema::create('Gebruiker', function (Blueprint $table) {
            $table->increments('Gebruiker_id');
            $table->text('naam')->nullable();
            $table->time('Tijd')->nullable();
            $table->integer('Score')->nullable();
            $table->string('Klas', 10)->nullable();
            $table->integer('geblevenVraag')->nullable();
            $table->integer('hintsGebruikt')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('Gebruiker');
    }
};
