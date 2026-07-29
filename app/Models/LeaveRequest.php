<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Enums\LeaveType;

class LeaveRequest extends Model
{
    protected $fillable = [
        'user_id',
        'type',
        'start_date',
        'end_date',
        'reason',
        'status',
        'approved_by',
        'approved_at',
        'admin_comment',
    ];

    protected  $casts = [
        'type' => LeaveType::class,
        'start_date' => 'date',
        'end_date' => 'date',
        'approved_at' => 'datetime',
    ];

    // 申請者
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // 承認した管理者
    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
