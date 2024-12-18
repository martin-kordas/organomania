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
        Schema::create('register_names', function (Blueprint $table) {
            $table->id();
            $table->foreignId('register_id')->constrained();
            $table->string('slug', length: 500);
            $table->string('name', length: 100);
            $table->string('language', length: 10);
            $table->integer('hide_language')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('register_names');
    }
};
