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
        Schema::create('estimation', function (Blueprint $table) {
            $table->id();
            $table->string('estimation_unique', 255)->unique();
            $table->unsignedBigInteger('vehicle_id')->nullable();
            $table->string('labour', 16)->nullable();
            $table->string('spare_part', 16)->nullable();
            $table->string('sublet', 16)->nullable();
            $table->string('total', 20)->nullable();
            $table->text('remark')->nullable();
            $table->enum('status',['Draft', 'New', 'Transfered'])->nullable();
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
        Schema::dropIfExists('estimation');
    }
};
