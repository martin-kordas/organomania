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
        Schema::create('organ_custom_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name', length: 100);
            $table->text('description')->nullable();
            $table->foreignId('user_id')->constrained();
            $table->timestamps();
            $table->softDeletes();
        });
        
        Schema::create('organ_organ_custom_category', function (Blueprint $table) {
            $table->foreignId('organ_id')
                ->constrained(indexName: 'fk_organ_id_custom')
                ->cascadeOnDelete();
            $table->foreignId('organ_custom_category_id')
                ->constrained(indexName: 'fk_organ_custom_category_id')
                ->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('organ_organ_custom_category');
        Schema::dropIfExists('organ_custom_categories');
    }
};
