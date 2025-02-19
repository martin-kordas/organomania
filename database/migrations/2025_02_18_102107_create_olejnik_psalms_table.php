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
        Schema::create('olejnik_psalms', function (Blueprint $table) {
            $table->id();
            $table->string('number', length: 5);
            $table->string('name', length: 150);
            $table->timestamps();
            
            $table->index(['number']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('olejnik_psalms');
    }
};
