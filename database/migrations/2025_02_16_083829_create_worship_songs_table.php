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
        Schema::create('worship_songs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organ_id')->constrained();
            $table->foreignId('song_id')->constrained()->onDelete('restrict');
            $table->date('date');
            $table->time('time')->nullable();
            $table->string('organist_name', length: 100)->nullable();
            $table->foreignId('user_id')->constrained()->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['organ_id', 'date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('worship_songs');
    }
};
