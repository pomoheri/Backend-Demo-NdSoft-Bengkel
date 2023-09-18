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
        Schema::create('work_order', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_code')->unique();
            $table->string('transaction_unique')->unique();
            $table->unsignedBigInteger('vehicle_id');
            $table->string('total')->nullable();
            $table->string('carrier')->nullable();
            $table->string('carrier_phone')->nullable();
            $table->enum('status', ['Draft', 'New', 'Outstanding','Closed', 'Approval', 'Void'])->nullable();
            $table->text('remark')->nullable();
            $table->string('technician')->nullable();
            $table->string('created_by', 150)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('work_order');
    }
};
