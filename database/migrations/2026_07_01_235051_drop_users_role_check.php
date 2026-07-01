<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() === 'pgsql') {
            // পুরোনো check constraint সরাও যাতে 'cr' সহ যেকোনো role বসে
            DB::statement('ALTER TABLE users DROP CONSTRAINT IF EXISTS users_role_check');
        }
    }

    public function down(): void
    {
        // ফিরিয়ে আনার দরকার নেই
    }
};