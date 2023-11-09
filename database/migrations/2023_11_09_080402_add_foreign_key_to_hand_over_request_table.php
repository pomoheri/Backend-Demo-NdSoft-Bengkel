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
        Schema::table('hand_over_request', function (Blueprint $table) {
            $table->foreign('hand_over_unique')->references('hand_over_unique')->on('hand_over')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hand_over_request', function (Blueprint $table) {
            $table->dropForeign('hand_over_unique');
        });
    }
};
