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
        Schema::create('organs', function (Blueprint $table) {
            $table->id();
            $table->string('slug', length: 500);
            $table->string('place', length: 500);
            $table->string('municipality', length: 50);
            $table->string('location_base_words', length: 200)->nullable();
            $table->double('latitude');
            $table->double('longitude');
            $table->foreignId('region_id')->nullable()->constrained();
            $table->integer('importance')->comment("1 (lowest) to 10 (greatest)");
            $table->foreignId('organ_builder_id')->nullable()->constrained();
            $table->string('organ_builder_name', length: 200)->nullable();
            $table->integer('year_built')->nullable();
            $table->foreignId('renovation_organ_builder_id')->nullable()->constrained('organ_builders');
            $table->string('renovation_organ_builder_name', length: 200)->nullable();
            $table->integer('year_renovated')->nullable();
            $table->foreignId('case_organ_builder_id')->nullable()->constrained('organ_builders');
            $table->string('case_organ_builder_name', length: 200)->nullable();
            $table->integer('case_year_built')->nullable();
            $table->integer('stops_count')->nullable();
            $table->integer('manuals_count')->nullable();
            $table->integer('original_stops_count')->nullable();
            $table->integer('original_manuals_count')->nullable();
            $table->integer('concert_hall')->default(0);
            $table->integer('preserved_organ')->default(1);
            $table->integer('preserved_case')->default(1);
            $table->string('image_url', length: 500)->nullable();
            $table->string('image_credits', length: 500)->nullable();
            $table->string('outside_image_url', length: 500)->nullable();
            $table->string('outside_image_credits', length: 500)->nullable();
            $table->text('web')->nullable();
            $table->text('place_web')->nullable();
            $table->integer('varhany_net_id')->nullable();
            $table->text('perex')->nullable();
            $table->text('description')->nullable();
            $table->text('literature')->nullable();
            $table->text('discography')->nullable();
            $table->text('disposition')->nullable();
            $table->integer('baroque')->default(0)->comment("From book Baroque organ-building in Moravia");
            $table->date('promotion_date')->nullable();
            $table->foreignId('user_id')->nullable()->constrained();
            $table->timestamps();
            $table->softDeletes();
            
            $table->fullText(['perex', 'description', 'place', 'municipality'], name: 'organs_perex_description_place_municipality_fulltext');
            $table->fullText('place');
            $table->fullText('municipality');
            $table->fullText('perex');
            $table->fullText('description');
            $table->fullText('disposition');
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
