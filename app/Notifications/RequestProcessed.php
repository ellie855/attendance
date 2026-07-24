<?php

namespace App\Notifications;

use App\Models\AttendanceCorrectionRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class RequestProcessed extends Notification
{
    use Queueable;

    public function __construct(public AttendanceCorrectionRequest $request)
    {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $statusLabel = match ($this->request->status) {
            'approved' => '承認されました',
            'rejected' => '却下されました',
            default => '処理されました',
        };

        return [
            'message' => "あなたの修正申請が{$statusLabel}",
            'type' => 'request_processed',
            'request_id' => $this->request->id,
            'status' => $this->request->status,
            'url' => route('attendance-requests.show', $this->request->id),
        ];
    }
}