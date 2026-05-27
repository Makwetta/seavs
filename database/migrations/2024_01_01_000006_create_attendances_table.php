<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// FILE NAME: database/migrations/2024_01_01_000006_create_attendance_table.php

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendances', function (Blueprint $table) {
            $table->increments('at_id');

            // Nullable: a rejected scan may not match any student
            $table->unsignedInteger('student_id')->nullable();

            $table->unsignedInteger('exam_id');

            // The supervisor / admin who ran the scanner
            $table->unsignedBigInteger('user_id');

            $table->dateTime('time')
                  ->comment('Exact timestamp of verification attempt');

            $table->enum('status', ['Verified', 'Rejected'])
                  ->default('Rejected');

            $table->timestamps();

            // ── Indexes ───────────────────────────────────────────
            $table->index('status');
            $table->index('time');
            $table->index(['exam_id', 'status']); // common dashboard query

            // ── Foreign keys ──────────────────────────────────────
            $table->foreign('student_id')
                  ->references('student_id')
                  ->on('students')
                  ->onDelete('set null');  // keep record even if student deleted

            $table->foreign('exam_id')
                  ->references('exam_id')
                  ->on('exams')
                  ->onDelete('cascade');  // delete attendance when exam deleted

            $table->foreign('user_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('restrict'); // don't delete user with attendance records
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendance');
    }
};
