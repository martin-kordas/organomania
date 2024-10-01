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
        Schema::create('stats', function (Blueprint $table) {
            $table->id();
            $table->dateTime('computed_on');
            $table->integer('users_count');
            $table->integer('organs_count');
            $table->integer('organ_builders_count');
            $table->integer('organ_custom_categories_count');
            $table->integer('organ_likes_count');
            $table->integer('organ_likes_max');
            $table->integer('organ_likes_max_organ_id');
            $table->float('organ_likes_avg');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stats');
    }
};
