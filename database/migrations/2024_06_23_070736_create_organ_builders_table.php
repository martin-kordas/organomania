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
            $table->string('short_name', length: 100)->nullable();
            $table->string('name_base_words', length: 200)->nullable();
            $table->string('place_of_birth', length: 50)->nullable();
            $table->string('place_of_death', length: 50)->nullable();
            $table->string('active_period', length: 50)->nullable();
            $table->integer('active_from_year');
            $table->string('municipality', length: 100);
            $table->double('latitude');
            $table->double('longitude');
            $table->foreignId('region_id')->nullable()->constrained();
            $table->string('workshop_members', length: 500)->nullable();
            $table->string('image_url', length: 500)->nullable();
            $table->string('image_credits', length: 500)->nullable();
            $table->text('web')->nullable();
            $table->integer('varhany_net_id')->nullable();
            $table->integer('importance')->comment("1 (lowest) to 10 (greatest)");
            $table->text('perex')->nullable();
            $table->text('description')->nullable();
            $table->text('literature')->nullable();
            $table->integer('baroque')->default(0)->comment("From book Baroque organ-building in Moravia");
            $table->foreignId('user_id')->nullable()->constrained();
            $table->timestamps();
            $table->softDeletes();
            
            $table->fullText(['perex', 'description', 'first_name', 'last_name', 'workshop_name'], name: 'organ_builders_perex_description_first_name_last_name_fulltext');
            $table->fullText('first_name');
            $table->fullText('last_name');
            $table->fullText('perex');
            $table->fullText('description');
            $table->fullText('workshop_name');
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
