<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// FILE NAME: database/migrations/2024_01_01_000005_create_exams_table.php

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exams', function (Blueprint $table) {
            $table->increments('exam_id');

            $table->string('name', 100)
                  ->comment('e.g. End of Semester Examination – DIT 2024');

            $table->unsignedInteger('subject_id');

            $table->date('exam_date');
            $table->time('exam_time');

            $table->timestamps();

            // ── Indexes ───────────────────────────────────────────
            $table->index('exam_date');  // fast filtering by date

            // ── Foreign keys ──────────────────────────────────────
            $table->foreign('subject_id')
                  ->references('subject_id')
                  ->on('subjects')
                  ->onDelete('restrict');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exams');
    }
};
