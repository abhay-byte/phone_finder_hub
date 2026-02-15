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
        Schema::table('phones', function (Blueprint $table) {
            $table->decimal('gpx_score', 8, 2)->nullable()->after('value_score');
            $table->index('gpx_score');
            $table->json('gpx_details')->nullable()->after('gpx_score'); // Store detailed breakdown
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('phones', function (Blueprint $table) {
            $table->dropIndex(['gpx_score']);
            $table->dropColumn(['gpx_score', 'gpx_details']);
        });
    }
};
