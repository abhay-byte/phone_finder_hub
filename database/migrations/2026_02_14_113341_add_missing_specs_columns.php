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
        Schema::table('spec_bodies', function (Blueprint $table) {
            $table->string('measured_display_brightness')->nullable()->after('display_brightness');
        });

        Schema::table('benchmarks', function (Blueprint $table) {
            $table->string('free_fall_rating')->nullable()->after('repairability_score');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('phones', function (Blueprint $table) {
            //
        });
    }
};
