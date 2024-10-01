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
        Schema::create('palette_registers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('register_id')->constrained();
            $table->foreignId('pitch_id')->nullable()->constrained();
            $table->string('multiplier', length: 100)->nullable();
            $table->integer('pedal')->default(0);
            $table->integer('frequent_manual')->default(0);
            $table->integer('frequent_pedal')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('palette_registers');
    }
};
