<?php

namespace App\Notifications;

use App\Models\LeaveRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class LeaveRequestProcessed extends Notification
{
    use Queueable;

    public function __construct(public LeaveRequest $leaveRequest)
    {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }
    public function toArray(object $notifiable): array
    {
        $statusLabel = match ($this->leaveRequest->status) {
            'approved' => ' 承認されました',
            'rejected' => '却下されました',
            default => '処理されました',
        };

        return [
            'message' => "あなたの休暇申請が{$statusLabel}",
            'type' => 'leave_request_processed',
            'request_id' => $this->leaveRequest->id,
            'status' => $this->leaveRequest->status,
            'url' => route('leave-requests.show', $this->leaveRequest->id),
        ];
    }
}