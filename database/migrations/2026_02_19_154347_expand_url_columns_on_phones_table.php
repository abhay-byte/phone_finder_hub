<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * URL columns on the phones table need to be TEXT, not VARCHAR(255).
     * Real-world Amazon/Flipkart/image URLs routinely exceed 255 characters
     * due to long query strings and tracking parameters.
     */
    public function up(): void
    {
        Schema::table('phones', function (Blueprint $table) {
            $table->text('image_url')->nullable()->change();
            $table->text('amazon_url')->nullable()->change();
            $table->text('flipkart_url')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('phones', function (Blueprint $table) {
            // Truncation risk on rollback, but this is the original schema
            $table->string('image_url', 255)->nullable()->change();
            $table->string('amazon_url', 255)->nullable()->change();
            $table->string('flipkart_url', 255)->nullable()->change();
        });
    }
};
