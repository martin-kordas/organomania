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
        Schema::create('dispositions', function (Blueprint $table) {
            $table->id();
            $table->string('slug', length: 500);
            $table->string('name', length: 200);
            $table->foreignId('organ_id')->nullable()->constrained();
            $table->text('appendix')->nullable();
            $table->text('description')->nullable();
            $table->integer('numbering')->default(0);
            $table->integer('keyboard_numbering')->default(0);
            $table->string('language', length: 10);
            $table->foreignId('user_id')->nullable()->constrained();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dispositions');
    }
};
