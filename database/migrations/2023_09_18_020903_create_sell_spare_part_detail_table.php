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
        Schema::create('sell_spare_part_detail', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_unique');
            $table->unsignedBigInteger('spare_part_id');
            $table->string('quantity')->nullable();
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
        Schema::dropIfExists('sell_spare_part_detail');
    }
};
