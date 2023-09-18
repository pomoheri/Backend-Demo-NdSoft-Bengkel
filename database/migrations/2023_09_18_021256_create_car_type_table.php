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
        Schema::create('car_type', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('car_brand_id')->nullable();
            $table->string('name')->nullable();
            $table->string('type')->unique()->nullable();
            $table->string('cc')->nullable();
            $table->string('engine_type')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('car_type');
    }
};
