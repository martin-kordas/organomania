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
        Schema::create('organ_rebuilds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organ_id')->constrained();
            $table->foreignId('organ_builder_id')->constrained();
            $table->integer('year_built');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('organ_rebuilds');
    }
};
