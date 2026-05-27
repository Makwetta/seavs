<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Exam extends Model
{
    use HasFactory;

    protected $primaryKey = 'exam_id';

    protected $fillable = [
        'name',
        'subject_id',
        'exam_date',
        'exam_time',
    ];

    protected $casts = [
        'exam_date' => 'date',
    ];

    // ── Relationships ────────────────────────────────────────────────

    public function subject()
    {
        return $this->belongsTo(Subject::class, 'subject_id', 'subject_id');
    }

    public function attendance()
    {
        return $this->hasMany(Attendance::class, 'exam_id', 'exam_id');
    }

    // ── Scopes ───────────────────────────────────────────────────────

    /** Filter exams happening today */
    public function scopeToday($query)
    {
        return $query->whereDate('exam_date', today());
    }

    /** Filter upcoming exams */
    public function scopeUpcoming($query)
    {
        return $query->whereDate('exam_date', '>=', today());
    }
}
