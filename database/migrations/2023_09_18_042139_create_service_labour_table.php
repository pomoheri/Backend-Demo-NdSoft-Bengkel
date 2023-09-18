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
        Schema::create('service_labour', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_unique')->nullable();
            $table->unsignedBigInteger('labour_id')->nullable();
            $table->string('frt')->nullable();
            $table->string('discount')->nullable();
            $table->string('subtotal')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_labour');
    }
};
