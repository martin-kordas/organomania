<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('organ_builder_additional_images', function (Blueprint $table) {
            $table->id();
            $table->string('name', length: 250);
            $table->foreignId('organ_builder_id')->nullable()->constrained();
            $table->string('image_url', length: 500);
            $table->string('image_credits', length: 500)->nullable();
            $table->integer('year_built')->nullable();
            $table->string('organ_builder_name', length: 250)->nullable();
            $table->string('details', length: 250)->nullable();
            $table->foreignId('case_organ_category_id')
                ->nullable()
                ->constrained(table: 'organ_categories');
            $table->integer('nonoriginal_case')->default(0);
            $table->integer('organ_exists')
                ->default(0)
                ->comment("Obrázek je již použit standardně u varhan, k aktuálnímu varhanáři byl přidán kvůli nejasnému autorství nebo kvůli příbuzenské souvislosti varhanářů.");
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('organ_builder_additional_images');
    }
};
