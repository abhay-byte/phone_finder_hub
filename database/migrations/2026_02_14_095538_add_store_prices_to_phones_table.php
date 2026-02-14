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
            $table->decimal('amazon_price', 10, 2)->nullable()->after('amazon_url');
            $table->decimal('flipkart_price', 10, 2)->nullable()->after('flipkart_url');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('phones', function (Blueprint $table) {
            $table->dropColumn(['amazon_price', 'flipkart_price']);
        });
    }
};
