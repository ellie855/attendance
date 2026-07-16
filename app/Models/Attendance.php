<?php

namespace App\Models;

use App\Enums\AttendanceType;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Attendance extends Model
{
    protected $fillable = ['user_id', 'type', 'created_at'];
    protected $casts = [
        'type' => AttendanceType::class,
    ];

    /**
     * この打刻者の所有者
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
