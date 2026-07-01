<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Postgres-এ role কে নিশ্চিতভাবে varchar বানাও
        if (DB::getDriverName() === 'pgsql') {
            DB::statement('ALTER TABLE users ALTER COLUMN role TYPE VARCHAR(255) USING role::text');
            DB::statement("ALTER TABLE users ALTER COLUMN role SET DEFAULT 'student'");
        }
    }

    public function down(): void
    {
    }
};