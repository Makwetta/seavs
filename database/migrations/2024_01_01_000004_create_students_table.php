<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// FILE NAME: database/migrations/2024_01_01_000004_create_students_table.php

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('students', function (Blueprint $table) {
            $table->increments('student_id');

            $table->string('reg_no', 50)->unique()
                  ->comment('University registration number e.g. IHET/DIT/2024/0001');

            $table->string('full_name', 100);

            $table->enum('gender', ['Male', 'Female']);

            $table->date('dob')->comment('Date of birth');

            // Encrypted biometric fingerprint template.
            // We store Laravel's encrypt() output, so TEXT is sufficient.
            // Raw fingerprint images are NEVER stored (PDPA 2022 compliance).
            $table->text('fingerprint')->nullable()
                  ->comment('AES-256 encrypted fingerprint template');

            $table->unsignedInteger('course_id');

            $table->timestamps();

            // ── Foreign keys ──────────────────────────────────────
            $table->foreign('course_id')
                  ->references('course_id')
                  ->on('courses')
                  ->onDelete('restrict'); // prevent deleting a course that has students
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
