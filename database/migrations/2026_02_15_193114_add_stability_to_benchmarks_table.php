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
        Schema::table('benchmarks', function (Blueprint $table) {
            $table->integer('dmark_wild_life_stress_stability')->nullable()->after('dmark_wild_life_extreme');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('benchmarks', function (Blueprint $table) {
            $table->dropColumn('dmark_wild_life_stress_stability');
        });
    }
};
