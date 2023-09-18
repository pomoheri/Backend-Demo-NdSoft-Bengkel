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
        Schema::create('service_invoice', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_code')->unique();
            $table->string('transaction_unique')->nullable();
            $table->string('payment_method')->nullable();
            $table->enum('payment_gateway', ['Cash','Transfer','QRIS','EDC'])->nullable();
            $table->enum('status', ['Draft', 'New', 'Outstanding','Closed','Approval','Void'])->nullable();
            $table->string('created_by', 150)->nullable();
            $table->string('closed_by', 150)->nullable();
            $table->date('closed_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_invoice');
    }
};
