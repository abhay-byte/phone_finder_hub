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
            $table->integer('ram_min')->nullable()->after('ram');
            $table->integer('ram_max')->nullable()->after('ram_min');
            $table->integer('storage_min')->nullable()->after('internal_storage');
            $table->integer('storage_max')->nullable()->after('storage_min');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('spec_platforms', function (Blueprint $table) {
            $table->dropColumn(['ram_min', 'ram_max', 'storage_min', 'storage_max']);
        });
    }
};
