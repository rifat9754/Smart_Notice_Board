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
        Schema::create('notices', function (Blueprint $table) {
$table->id();
$table->string('title');
$table->text('body');
$table->enum('type', ['text', 'image', 'pdf'])->default('text');
$table->string('file_path')->nullable();
$table->enum('priority', ['high', 'medium', 'low'])->default('medium');
$table->enum('status', ['draft', 'published', 'expired'])->default('draft');
$table->boolean('is_emergency')->default(false);
$table->date('show_from')->nullable();
$table->date('show_to')->nullable();
$table->time('time_start')->nullable();
$table->time('time_end')->nullable();
$table->text('ai_summary')->nullable();
$table->foreignId('author_id')->constrained('users')->cascadeOnDelete();
$table->foreignId('board_id')->nullable()->constrained('boards')->nullOnDelete();
$table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notices');
    }
};
