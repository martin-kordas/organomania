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
        Schema::create('varhany_net_organ_builders', function (Blueprint $table) {
            $table->id();
            $table->integer('varhany_net_id');
            $table->datetime('scraped_at');
            $table->text('data');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('varhany_net_organ_builders');
    }
};
