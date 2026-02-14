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
            $table->boolean('bootloader_unlockable')->default(true)->after('storage_type');
            $table->boolean('turnip_support')->default(true)->after('bootloader_unlockable');
            $table->integer('aosp_aesthetics_score')->default(8)->after('turnip_support');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('spec_platforms', function (Blueprint $table) {
            $table->dropColumn(['bootloader_unlockable', 'turnip_support', 'aosp_aesthetics_score']);
        });
    }
};
