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
        Schema::create('disposition_registers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('keyboard_id')->constrained()
                ->cascadeOnDelete();
            $table->integer('order');
            $table->foreignId('register_name_id')->nullable()->constrained();
            $table->string('name', length: 100)->nullable();
            $table->integer('coupler')->default(0);
            $table->string('multiplier', length: 100)->nullable();
            $table->foreignId('pitch_id')->nullable()->constrained();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('disposition_registers');
    }
};
