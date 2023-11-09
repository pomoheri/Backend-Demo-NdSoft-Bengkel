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
        Schema::create('hand_over', function (Blueprint $table) {
            $table->id();
            $table->string('hand_over_unique', 255)->unique();
            $table->unsignedBigInteger('vehicle_id')->nullable();
            $table->enum('status',['Draft', 'New', 'Transfered','Closed'])->nullable();
            $table->string('created_by', 200)->nullable();
            $table->string('updated_by', 200)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hand_over');
    }
};
