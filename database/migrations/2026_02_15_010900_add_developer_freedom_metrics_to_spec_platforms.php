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
        Schema::table('spec_platforms', function (Blueprint $table) {
            $table->string('turnip_support_level')->nullable()->after('turnip_support'); // Full, Stable, Partial, None
            $table->string('os_openness')->nullable()->after('os_details'); // Near-AOSP, Moderate, Restricted
            $table->string('gpu_emulation_tier')->nullable()->after('gpu'); // Adreno 8xx, Adreno 7xx, Immortalis G925, etc.
            $table->string('custom_rom_support')->nullable()->after('aosp_aesthetics_score'); // Major, Limited, None
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('spec_platforms', function (Blueprint $table) {
            $table->dropColumn(['turnip_support_level', 'os_openness', 'gpu_emulation_tier', 'custom_rom_support']);
        });
    }
};
