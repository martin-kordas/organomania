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
        Schema::create('organ_builder_timeline_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organ_builder_id')
                ->constrained(indexName: 'fk_organ_builder_timeline_items_organ_builder_id');
            $table->string('name', length: 400);
            $table->integer('year_from');
            $table->integer('year_to')->nullable();
            $table->string('active_period', length: 50)->nullable();
            $table->integer('is_workshop')->default(0);
            $table->string('locality', length: 100)->nullable();
            $table->string('land', length: 50)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('organ_builder_timeline_items');
    }
};
