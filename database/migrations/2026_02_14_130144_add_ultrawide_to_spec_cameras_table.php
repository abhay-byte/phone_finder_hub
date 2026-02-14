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
        Schema::table('spec_cameras', function (Blueprint $table) {
            $table->string('ultrawide_camera_specs')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('spec_cameras', function (Blueprint $table) {
            $table->dropColumn('ultrawide_camera_specs');
        });
    }
};
