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
    Schema::table('notices', function (Blueprint $table) {
        $table->string('year')->nullable()->after('notified_seen');       // 1st/2nd/3rd/4th
        $table->string('section')->nullable()->after('year');             // A / B
    });
}

    /**
     * Reverse the migrations.
     */
public function down(): void
{
    Schema::table('notices', function (Blueprint $table) {
        $table->dropColumn(['year', 'section']);
    });
}
};
