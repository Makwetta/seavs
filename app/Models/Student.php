<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $primaryKey = 'student_id';

    protected $fillable = [
        'reg_no',
        'full_name',
        'gender',
        'dob',
        'fingerprint',
        'course_id',
    ];

    protected $hidden = [
        'fingerprint', // never expose raw encrypted biometric data in JSON
    ];

    // ── Relationships ────────────────────────────────────────────────

    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id', 'course_id');
    }

    public function attendance()
    {
        return $this->hasMany(Attendance::class, 'student_id', 'student_id');
    }

    // ── Accessors ────────────────────────────────────────────────────

    /** Returns true if a fingerprint template has been enrolled */
    public function getIsEnrolledAttribute(): bool
    {
        return !is_null($this->fingerprint);
    }
}
