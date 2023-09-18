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
        Schema::create('sell_spare_part', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_code')->unique();
            $table->string('transaction_unique')->unique();
            $table->string('name')->nullable();
            $table->string('phone', 15)->nullable();
            $table->string('address')->nullable();
            $table->string('total')->nullable();
            $table->date('payment_date')->nullable();
            $table->enum('payment_method',['Cash', 'Kredit'])->nullable();
            $table->enum('payment_gateway',['Cash', 'Transfer', 'QRIS', 'EDC'])->nullable();
            $table->enum('status',['Draft','New','Outstanding','Closed','Approval','Void'])->nullable();
            $table->text('remark')->nullable();
            $table->string('created_by', 150)->nullable();
            $table->string('closed_by', 150)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sell_spare_part');
    }
};
