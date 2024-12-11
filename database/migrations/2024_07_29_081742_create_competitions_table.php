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
        Schema::create('competitions', function (Blueprint $table) {
            $table->id();
            $table->string('slug', length: 500);
            $table->string('name', length: 100);
            $table->string('locality', length: 50)->nullable();
            $table->string('place', length: 500)->nullable();
            $table->double('latitude');
            $table->double('longitude');
            $table->integer('region_id')->nullable();
            $table->string('frequency')->nullable();
            $table->integer('max_age')->nullable();
            $table->decimal('participation_fee')->nullable();
            $table->decimal('participation_fee_eur')->nullable();
            $table->decimal('first_prize')->nullable();
            $table->integer('next_year')->nullable();
            $table->integer('inactive');
            $table->integer('international');
            $table->string('image_url', length: 500)->nullable();
            $table->string('image_credits', length: 500)->nullable();
            $table->text('url')->nullable();
            $table->text('perex')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
        
        Schema::create('competition_organ', function (Blueprint $table) {
            $table->foreignId('competition_id')
                ->constrained(indexName: 'fk_competition_organ_competition_id');
            $table->foreignId('organ_id')
                ->constrained(indexName: 'fk_competition_organ_organ_id');
            $table->timestamps();
        });
        
        Schema::create('competition_years', function (Blueprint $table) {
            $table->id();
            $table->foreignId('competition_id')->constrained()
                ->cascadeOnDelete();
            $table->integer('year');
            $table->text('description');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('competition_years');
        Schema::dropIfExists('competition_organ');
        Schema::dropIfExists('competitions');
    }
};
