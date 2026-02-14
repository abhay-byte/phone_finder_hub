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
        Schema::create('spec_cameras', function (Blueprint $table) {
            $table->id();
            $table->foreignId('phone_id')->constrained()->onDelete('cascade');
            // Main Camera
            $table->text('main_camera_specs')->nullable(); // e.g. "50 MP, f/1.6..."
            $table->text('main_camera_features')->nullable();
            $table->string('main_video_capabilities')->nullable();
            // Selfie Camera
            $table->text('selfie_camera_specs')->nullable();
            $table->text('selfie_camera_features')->nullable();
            $table->string('selfie_video_capabilities')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('spec_cameras');
    }
};
