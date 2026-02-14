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
            $table->string('screen_area')->nullable();
            $table->string('aspect_ratio')->nullable();
            $table->string('glass_protection_level')->nullable();
        });

        Schema::table('spec_cameras', function (Blueprint $table) {
            $table->string('main_camera_zoom')->nullable();
            $table->string('main_camera_pdaf')->nullable();
            $table->string('selfie_video_features')->nullable();
        });

        Schema::table('spec_connectivities', function (Blueprint $table) {
            $table->text('positioning_details')->nullable(); // Changed to text for long string
            $table->boolean('has_3_5mm_jack')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('spec_bodies', function (Blueprint $table) {
            $table->dropColumn(['screen_area', 'aspect_ratio', 'glass_protection_level']);
        });

        Schema::table('spec_cameras', function (Blueprint $table) {
            $table->dropColumn(['main_camera_zoom', 'main_camera_pdaf', 'selfie_video_features']);
        });

        Schema::table('spec_connectivities', function (Blueprint $table) {
            $table->dropColumn(['positioning_details', 'has_3_5mm_jack']);
        });
    }
};
