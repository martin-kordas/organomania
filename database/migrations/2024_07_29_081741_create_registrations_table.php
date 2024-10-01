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
        Schema::create('registrations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('disposition_id')->constrained()
                ->cascadeOnDelete();
            $table->foreignId('base_registration_id')
                ->nullable()
                ->constrained('registrations')
                ->cascadeOnDelete();
            $table->integer('order')->nullable();
            $table->string('name', length: 200);
            $table->foreignId('user_id')->nullable()->constrained();
            $table->timestamps();
            $table->softDeletes();
        });
        
        Schema::create('registration_disposition_register', function (Blueprint $table) {
            $table->foreignId('registration_id')
                ->constrained(indexName: 'fk_registration_id');
            $table->foreignId('disposition_register_id')
                ->constrained(indexName: 'fk_disposition_register_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('registration_disposition_register');
        Schema::dropIfExists('registrations');
    }
};
