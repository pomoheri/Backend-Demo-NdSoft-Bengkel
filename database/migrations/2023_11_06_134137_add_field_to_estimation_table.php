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
        Schema::table('estimation', function (Blueprint $table) {
            $table->string('carrier', 150)->nullable()->after('vehicle_id');
            $table->string('carrier_phone', 15)->nullable()->after('carrier');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('estimation', function (Blueprint $table) {
            $table->dropColumn('carrier');
            $table->dropColumn('carrier_phone');
        });
    }
};
