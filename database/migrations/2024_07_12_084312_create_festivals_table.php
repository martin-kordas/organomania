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
        Schema::create('festivals', function (Blueprint $table) {
            $table->id();
            $table->string('name', length: 100);
            $table->string('locality', length: 50)->nullable();
            $table->string('place', length: 500)->nullable();
            $table->double('latitude');
            $table->double('longitude');
            $table->integer('region_id')->nullable();
            $table->foreignId('organ_id')->nullable()->constrained();
            $table->string('frequency')->nullable();
            $table->string('url', length: 500)->nullable();
            $table->integer('importance')->comment("1 (lowest) to 10 (greatest)");
            $table->text('perex')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('festivals');
    }
};
