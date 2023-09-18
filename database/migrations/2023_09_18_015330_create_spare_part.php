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
        Schema::create('spare_part', function (Blueprint $table) {
            $table->id();
            $table->string('part_number')->unique();
            $table->string('name')->nullable();
            $table->unsignedBigInteger('car_brand_id')->nullable();
            $table->enum('grade', ['Genuine', 'Non Genuine'])->nullable();
            $table->enum('category', ['Spare Part', 'Material'])->nullable();
            $table->string('stock')->nullable();
            $table->string('buying_price')->nullable();
            $table->string('selling_price')->nullable();
            $table->string('profit')->nullable();
            $table->unsignedBigInteger('location_id')->nullable();
            $table->string('created_by', 150)->nullable();
            $table->string('updated_by', 150)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('spare_part');
    }
};
