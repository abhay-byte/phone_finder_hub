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
        Schema::create('spec_bodies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('phone_id')->constrained()->onDelete('cascade');
            $table->string('dimensions')->nullable();
            $table->string('weight')->nullable();
            $table->string('build_material')->nullable();
            $table->string('sim')->nullable();
            $table->string('ip_rating')->nullable();
            $table->string('colors')->nullable();
            // Display specs
            $table->string('display_type')->nullable();
            $table->string('display_size')->nullable();
            $table->string('display_resolution')->nullable();
            $table->string('display_protection')->nullable();
            $table->text('display_features')->nullable(); // For things like "120Hz, HDR10+"
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('spec_bodies');
    }
};
