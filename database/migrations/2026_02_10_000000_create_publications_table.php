<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::create('publication_types', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
        });

        Schema::create('publication_topics', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
        });

        Schema::create('publications', function (Blueprint $table) {
            $table->id();
            $table->string('name', length: 500);
            $table->string('name_cz', length: 500)->nullable();
            $table->string('place_of_publication', length: 100);
            $table->integer('year');
            $table->string('language', length: 10);
            $table->foreignId('publication_type_id')->constrained();
            $table->foreignId('publication_topic_id')->constrained();
            $table->foreignId('region_id')->nullable()->constrained();
            $table->foreignId('organ_id')->nullable()->constrained();
            $table->foreignId('organ_builder_id')->nullable()->constrained();
            $table->string('journal', length: 500)->nullable();
            $table->string('journal_issue', length: 100)->nullable();
            $table->integer('journal_is_book')->default(0);
            $table->string('thesis_description', length: 500)->nullable();
            $table->string('citation', length: 500)->nullable();
            $table->string('url', length: 500)->nullable();
            $table->string('library_url', length: 500)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('authors', function (Blueprint $table) {
            $table->id();
            $table->string('first_name', length: 100);
            $table->string('last_name', length: 100);
            $table->integer('year_of_birth')->nullable();
            $table->integer('year_of_death')->nullable();
            $table->string('description', length: 500)->nullable();
            $table->string('cv_url', length: 500)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('publication_author', function (Blueprint $table) {
            $table->id();
            $table->integer('order')->default(1);
            $table->foreignId('publication_id')->constrained()->cascadeOnDelete();
            $table->foreignId('author_id')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('publication_author');
        Schema::dropIfExists('authors');
        Schema::dropIfExists('publications');
        Schema::dropIfExists('publication_topics');
        Schema::dropIfExists('publication_types');
    }
};
