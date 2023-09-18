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
        Schema::create('small_transaction', function (Blueprint $table) {
            $table->id();
            $table->date('date')->nullable();
            $table->string('description')->nullable();
            $table->string('pic')->nullable();
            $table->enum('status',['Debit','Kredit'])->nullable();
            $table->enum('category',['Cost','Sublet','Asset','Kas','Modal','Prive','SPM'])->nullable();
            $table->string('total')->nullable();
            $table->string('created_by')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('small_transaction');
    }
};
