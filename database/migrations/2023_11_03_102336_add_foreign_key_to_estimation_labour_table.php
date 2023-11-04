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
        Schema::table('estimation_labour', function (Blueprint $table) {
            $table->foreign('labour_id')->references('id')->on('labour')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('estimation_labour', function (Blueprint $table) {
            $table->dropForeign('labour_id');
        });
    }
};
