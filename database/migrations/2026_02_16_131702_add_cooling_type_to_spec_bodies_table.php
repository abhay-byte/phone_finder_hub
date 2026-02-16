<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('spec_bodies', function (Blueprint $table) {
            $table->string('cooling_type')->nullable()->after('build_material');
        });
    }

    public function down(): void
    {
        Schema::table('spec_bodies', function (Blueprint $table) {
            $table->dropColumn('cooling_type');
        });
    }
};
