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
            
            $table->integer('organs_views');
            $table->integer('organ_builders_views');
            $table->integer('dispositions_views');
            $table->integer('festivals_views');
            $table->integer('competitions_views');
            
            $table->integer('organs_viewed_last_day');
            $table->integer('organ_builders_viewed_last_day');
            $table->integer('dispositions_viewed_last_day');
            $table->integer('festivals_viewed_last_day');
            $table->integer('competitions_viewed_last_day');
            
            $table->integer('organs_viewed_last_week');
            $table->integer('organ_builders_viewed_last_week');
            $table->integer('dispositions_viewed_last_week');
            $table->integer('festivals_viewed_last_week');
            $table->integer('competitions_viewed_last_week');
            
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
