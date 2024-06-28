<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**s
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('organs', function (Blueprint $table) {
            $table->id();
            $table->string('place', length: 500);
            $table->string('municipality', length: 50);
            $table->double('latitude');
            $table->double('longitude');
            $table->foreignId('region_id')->constrained();
            $table->integer('importance')->comment("1 (lowest) to 10 (greatest)");
            $table->foreignId('organ_builder_id')->nullable()->constrained();
            $table->integer('year_built')->nullable();
            $table->integer('stops_count')->nullable();
            $table->integer('manuals_count')->nullable();
            $table->string('image_url', length: 500)->nullable();
            $table->text('perex')->nullable();
            $table->text('description')->nullable();
            $table->foreignId('user_id')->nullable()->constrained();
            $table->timestamps();
            $table->softDeletes();
            
            $table->fullText('description');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('organs');
    }
};
