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
        Schema::table('car_type', function (Blueprint $table) {
            $table->foreign('car_brand_id')->references('id')->on('car_brand')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('car_type', function (Blueprint $table) {
            $table->dropForeign('car_brand_id');
        });
    }
};
