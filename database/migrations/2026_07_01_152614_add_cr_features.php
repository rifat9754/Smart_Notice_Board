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
   
    Schema::table('users', function (Blueprint $table) {
        $table->string('role')->default('student')->change();
    });

   
    Schema::table('notices', function (Blueprint $table) {
        $table->foreignId('notified_teacher_id')->nullable()->after('author_id')
              ->constrained('users')->nullOnDelete();
        $table->boolean('notified_seen')->default(false)->after('notified_teacher_id');
    });
}

public function down(): void
{
    Schema::table('notices', function (Blueprint $table) {
        $table->dropForeign(['notified_teacher_id']);
        $table->dropColumn(['notified_teacher_id', 'notified_seen']);
    });
}
};
