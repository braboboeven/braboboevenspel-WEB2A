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
        Schema::create('Misdaad', function (Blueprint $table) {
            $table->integer('misdaad_id')->primary();
            $table->integer('verdachte_id');
            $table->string('misdaad_type', 45);
            $table->date('datum_gepleegd');
            $table->string('gevangenis', 45);
            $table->string('gedrag', 45)->nullable();
            $table->date('start_datum');
            $table->date('eind_datum')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('Misdaad');
    }
};
