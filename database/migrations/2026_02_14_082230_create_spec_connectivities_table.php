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
        Schema::create('spec_connectivities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('phone_id')->constrained()->onDelete('cascade');
            $table->string('wlan')->nullable();
            $table->string('bluetooth')->nullable();
            $table->string('positioning')->nullable();
            $table->string('nfc')->nullable();
            $table->string('infrared')->nullable();
            $table->string('radio')->nullable();
            $table->string('usb')->nullable();
            $table->text('sensors')->nullable();
            $table->string('loudspeaker')->nullable();
            $table->string('jack_3_5mm')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('spec_connectivities');
    }
};
