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
        Schema::create('estimation_labour', function (Blueprint $table) {
            $table->id();
            $table->string('estimation_unique')->nullable();
            $table->unsignedBigInteger('labour_id')->nullable();
            $table->string('frt', 10)->nullable();
            $table->string('subtotal', 16)->nullable();
            $table->string('discount', 16)->nullable();
            $table->string('total', 16)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('estimation_labour');
    }
};
