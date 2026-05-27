<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    protected $table = 'attendances';
    protected $primaryKey = 'at_id';

    protected $fillable = [
        'student_id',
        'exam_id',
        'user_id',
        'time',
        'status',
    ];

    protected $casts = [
        'time' => 'datetime',
    ];

    // ── Relationships ────────────────────────────────────────────────

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id', 'student_id');
    }

    public function exam()
    {
        return $this->belongsTo(Exam::class, 'exam_id', 'exam_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // ── Scopes ───────────────────────────────────────────────────────

    public function scopeVerified($query)
    {
        return $query->where('status', 'Verified');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'Rejected');
    }

    public function scopeToday($query)
    {
        return $query->whereDate('time', today());
    }
}
