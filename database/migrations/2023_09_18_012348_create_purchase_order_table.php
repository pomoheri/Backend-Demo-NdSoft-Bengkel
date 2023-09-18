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
        Schema::create('purchase_order', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_code', 100)->unique()->nullable();
            $table->string('transaction_unique', 100)->unique()->nullable();
            $table->unsignedBigInteger('supplier_id');
            $table->string('invoice_number', 150)->nullable();
            $table->date('invoice_date')->nullable();
            $table->string('total')->nullable();
            $table->enum('status', ['Draft', 'New', 'Outstanding','Paid','Approval','Void'])->nullable();
            $table->text('remark')->nullable();
            $table->string('created_by', 150)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_order');
    }
};
