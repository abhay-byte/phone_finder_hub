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
            $table->index('overall_score');
            $table->index('ueps_score');
            $table->index('price');
            $table->index('release_date');
        });

        Schema::table('benchmarks', function (Blueprint $table) {
            $table->index('antutu_score');
            $table->index('geekbench_single');
            $table->index('geekbench_multi');
            $table->index('dmark_wild_life_extreme');
            $table->index('battery_endurance_hours');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('phones', function (Blueprint $table) {
            $table->dropIndex(['overall_score']);
            $table->dropIndex(['ueps_score']);
            $table->dropIndex(['price']);
            $table->dropIndex(['release_date']);
        });

        Schema::table('benchmarks', function (Blueprint $table) {
            $table->dropIndex(['antutu_score']);
            $table->dropIndex(['geekbench_single']);
            $table->dropIndex(['geekbench_multi']);
            $table->dropIndex(['dmark_wild_life_extreme']);
            $table->dropIndex(['battery_endurance_hours']);
        });
    }
};
