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
        Schema::create('organ_builder_categories', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
        });
    
        Schema::create('organ_builder_organ_builder_category', function (Blueprint $table) {
            $table->foreignId('organ_builder_id')
                ->constrained(indexName: 'fk_organ_builder_id')
                ->cascadeOnDelete();
            $table->foreignId('organ_builder_category_id')
                ->constrained(indexName: 'fk_organ_builder_category_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('organ_builder_organ_builder_category');
        Schema::dropIfExists('organ_builder_categories');
    }
};
