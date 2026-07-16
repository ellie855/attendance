<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttendanceCorrectionRequest extends Model
{
    protected $fillable = [
        'user_id',
        'target_attendance_id',
        'new_time',
        'reason',
        'status',
        'approved_by',
        'approved_at',
        'admin_comment',
    ];

    protected $casts = [
        'new_time' => 'datetime',
        'approved_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function targetAttendance()
    {
        return $this->belongsTo(Attendance::class, 'target_attendance_id');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
