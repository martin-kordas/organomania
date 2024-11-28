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
        Schema::create('registration_sets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('disposition_id')->constrained();
            $table->string('slug', length: 500);
            $table->string('name', length: 100);
            $table->foreignId('user_id')->nullable()->constrained();
            $table->timestamps();
            $table->softDeletes();
        });
        
        Schema::create('registration_set_registration', function (Blueprint $table) {
            $table->foreignId('registration_set_id')
                ->constrained();
            $table->foreignId('registration_id')
                ->constrained();
            $table->integer('order');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('registration_set_registration');
        Schema::dropIfExists('registration_sets');
    }
};
