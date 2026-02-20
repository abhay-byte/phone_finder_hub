<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // For PostgreSQL where enum is handled either as varchar or check constraint.
        // Drop constraint if Laravel created one.
        DB::statement('ALTER TABLE users DROP CONSTRAINT IF EXISTS users_role_check');
        // Add new check constraint
        DB::statement("ALTER TABLE users ADD CONSTRAINT users_role_check CHECK (role::text = ANY (ARRAY['user'::character varying, 'super_admin'::character varying, 'author'::character varying]::text[]))");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('ALTER TABLE users DROP CONSTRAINT IF EXISTS users_role_check');
        DB::statement("ALTER TABLE users ADD CONSTRAINT users_role_check CHECK (role::text = ANY (ARRAY['user'::character varying, 'super_admin'::character varying]::text[]))");
    }
};
