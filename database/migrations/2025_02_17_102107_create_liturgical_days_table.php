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
        Schema::create('liturgical_days', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->string('season', length: 50);
            $table->string('lectionary', length: 1)->nullable();
            $table->integer('ferial_lectionary')->nullable();
            $table->timestamps();
        });
        
        Schema::create('liturgical_celebrations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('liturgical_day_id')->constrained()->onDelete('cascade');
            $table->string('name', length: 200);
            $table->string('rank', length: 50);
            $table->string('color', length: 50);
            $table->integer('psalm_olejnik')->nullable();
            $table->string('psalm_korejs', length: 100)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('liturgical_celebrations');
        Schema::dropIfExists('liturgical_days');
    }
};
