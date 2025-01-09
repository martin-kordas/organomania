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
        Schema::create('organists', function (Blueprint $table) {
            $table->id();
            $table->string('first_name', length: 50)->nullable();
            $table->string('last_name', length: 50);
            $table->integer('year_of_birth')->nullable();
            $table->text('occupation')->nullable();
            $table->string('channel_character', length: 300)->nullable();
            $table->string('channel_username', length: 300);
            $table->string('channel_id', length: 300);
            $table->string('facebook', length: 300)->nullable();
            $table->string('web', length: 300)->nullable();
            $table->integer('subscribers_count')->nullable();
            $table->integer('videos_count')->nullable();
            $table->string('avatar_url', length: 300)->nullable();
            $table->date('last_video_date')->nullable();
            $table->string('last_video_name', length: 500)->nullable();
            $table->string('last_video_id', length: 500)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('organists');
    }
};
