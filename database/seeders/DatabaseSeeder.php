<?php

// FILE: database/seeders/DatabaseSeeder.php
// Run with: php artisan db:seed

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Course;
use App\Models\Subject;
use App\Models\Student;
use App\Models\Exam;
use App\Models\Attendance;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ── 1. System Users ───────────────────────────────────────────────
        $admin = User::firstOrCreate(
            ['email' => 'admin@ihet.ac.tz'],
            [
                'name'     => 'Alexander Samwel',
                'role'     => 'admin',
                'password' => Hash::make('Admin@1234'),
            ]
        );

        $supervisor = User::firstOrCreate(
            ['email' => 'supervisor@ihet.ac.tz'],
            [
                'name'     => 'Mary Josephine',
                'role'     => 'supervisor',
                'password' => Hash::make('Super@1234'),
            ]
        );

        // ── 2. Courses ────────────────────────────────────────────────────
        $courses = [
            'Diploma in Information Technology',
            'Diploma in Business Administration',
            'Diploma in Electrical Engineering',
            'Diploma in Civil Engineering',
            'Diploma in Mechanical Engineering',
        ];

        $courseModels = [];
        foreach ($courses as $name) {
            $courseModels[] = Course::firstOrCreate(['name' => $name]);
        }

        // ── 3. Subjects ───────────────────────────────────────────────────
        $subjects = [
            'Database Management Systems',
            'Web Development',
            'Computer Networks',
            'Software Engineering',
            'Operating Systems',
            'Mathematics for Computing',
            'Technical Communication',
            'Entrepreneurship',
        ];

        $subjectModels = [];
        foreach ($subjects as $name) {
            $subjectModels[] = Subject::firstOrCreate(['name' => $name]);
        }

        // ── 4. Students ───────────────────────────────────────────────────
        $studentsData = [
            ['IHET/DIT/2024/0001', 'Yuaja Nehemia Makweta',  'Male',   '2003-05-14'],
            ['IHET/DIT/2024/0002', 'Irene Jerome Vumu',      'Female', '2002-11-20'],
            ['IHET/DIT/2024/0003', 'Ibrahim Ramadhani Sombi','Male',   '2003-03-08'],
            ['IHET/DIT/2024/0004', 'Fatuma Ally Hassan',     'Female', '2002-07-25'],
            ['IHET/DIT/2024/0005', 'John Michael Masawe',    'Male',   '2001-12-01'],
            ['IHET/DIT/2024/0006', 'Grace Emmanuel Mwanga',  'Female', '2003-01-30'],
            ['IHET/DIT/2024/0007', 'Peter Salum Msangi',     'Male',   '2002-09-18'],
            ['IHET/DIT/2024/0008', 'Amina Said Juma',        'Female', '2003-06-05'],
        ];

        $studentModels = [];
        foreach ($studentsData as [$reg, $name, $gender, $dob]) {
            $studentModels[] = Student::firstOrCreate(
                ['reg_no' => $reg],
                [
                    'full_name' => $name,
                    'gender'    => $gender,
                    'dob'       => $dob,
                    'course_id' => $courseModels[0]->course_id, // all in DIT for demo
                    'fingerprint' => null, // enrolled via scanner in real use
                ]
            );
        }

        // ── 5. Exams ──────────────────────────────────────────────────────
        $examsData = [
            ['End of Semester Exam – DBMS 2024',        0, now()->toDateString(),                 '08:00:00'],
            ['End of Semester Exam – Web Dev 2024',     1, now()->toDateString(),                 '10:00:00'],
            ['End of Semester Exam – Networks 2024',    2, now()->addDays(1)->toDateString(),     '08:00:00'],
            ['End of Semester Exam – Soft Eng 2024',    3, now()->addDays(2)->toDateString(),     '10:00:00'],
            ['Supplementary Exam – Mathematics 2024',   5, now()->subDays(3)->toDateString(),     '14:00:00'],
        ];

        $examModels = [];
        foreach ($examsData as [$name, $subIdx, $date, $time]) {
            $examModels[] = Exam::firstOrCreate(
                ['name' => $name],
                [
                    'subject_id' => $subjectModels[$subIdx]->subject_id,
                    'exam_date'  => $date,
                    'exam_time'  => $time,
                ]
            );
        }

        // ── 6. Sample Attendance Records ──────────────────────────────────
        // Add some demo verified/rejected records for the dashboard
        $sampleAttendance = [
            [$studentModels[0], $examModels[0], 'Verified'],
            [$studentModels[1], $examModels[0], 'Verified'],
            [$studentModels[2], $examModels[0], 'Rejected'],
            [$studentModels[3], $examModels[0], 'Verified'],
            [$studentModels[4], $examModels[1], 'Verified'],
            [$studentModels[5], $examModels[1], 'Rejected'],
        ];

        foreach ($sampleAttendance as [$student, $exam, $status]) {
            Attendance::firstOrCreate(
                [
                    'student_id' => $student->student_id,
                    'exam_id'    => $exam->exam_id,
                ],
                [
                    'user_id' => $supervisor->id,
                    'time'    => now()->subMinutes(rand(1, 120)),
                    'status'  => $status,
                ]
            );
        }

        $this->command->info('✅ Database seeded successfully!');
        $this->command->info('');
        $this->command->info('  Admin login:      admin@ihet.ac.tz      / Admin@1234');
        $this->command->info('  Supervisor login: supervisor@ihet.ac.tz / Super@1234');
    }
}
