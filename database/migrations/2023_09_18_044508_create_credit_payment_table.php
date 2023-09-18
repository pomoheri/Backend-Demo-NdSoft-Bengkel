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
        Schema::create('credit_payment', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_unique')->nullable();
            $table->date('date');
            $table->string('total')->nullable();
            $table->string('amount')->nullable();
            $table->string('balance')->nullable();
            $table->string('created_by')->nullable();
            $table->text('remark')->nullable();
            $table->string('deleted_by')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('credit_payment');
    }
};
