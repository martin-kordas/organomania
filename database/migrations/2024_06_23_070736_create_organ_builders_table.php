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
        Schema::create('organ_builders', function (Blueprint $table) {
            $table->id();
            $table->string('slug', length: 500);
            $table->integer('is_workshop');
            $table->string('workshop_name', length: 100)->nullable();
            $table->string('first_name', length: 100)->nullable();
            $table->string('last_name', length: 100)->nullable();
            $table->string('place_of_birth', length: 50)->nullable();
            $table->string('place_of_death', length: 50)->nullable();
            $table->string('active_period', length: 50)->nullable();
            $table->integer('active_from_year');
            $table->string('municipality', length: 50);
            $table->double('latitude');
            $table->double('longitude');
            $table->foreignId('region_id')->nullable()->constrained();
            $table->string('workshop_members', length: 500)->nullable();
            $table->string('image_url', length: 500)->nullable();
            $table->string('image_credits', length: 500)->nullable();
            $table->string('web', length: 500)->nullable();
            $table->integer('varhany_net_id')->nullable();
            $table->integer('importance')->comment("1 (lowest) to 10 (greatest)");
            $table->text('perex')->nullable();
            $table->text('description')->nullable();
            $table->text('literature')->nullable();
            $table->foreignId('user_id')->nullable()->constrained();
            $table->timestamps();
            $table->softDeletes();
            
            $table->fullText('perex');
            $table->fullText('description');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('organ_builders');
    }
};
