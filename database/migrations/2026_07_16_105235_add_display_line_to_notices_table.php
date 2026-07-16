<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
{
    Schema::table('notices', function (Blueprint $table) {
        $table->string('display_line', 255)->nullable();   // এক লাইন, board-এর জন্য
    });
}

public function down(): void
{
    Schema::table('notices', function (Blueprint $table) {
        $table->dropColumn('display_line');
    });
}
};
