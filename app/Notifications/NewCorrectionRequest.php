<?php

namespace App\Notifications;

use App\Models\AttendanceCorrectionRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewCorrectionRequest extends Notification
{
    use Queueable;

    public function __construct(public AttendanceCorrectionRequest $request)
    {
    }

    // どこに通知を保存するか(今はDBだけ)
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    // DB に保存する内容
    public function toArray(object $notifiable): array
    {
        return [
            'message' => "{$this->request->user->name}さんから修正申請が届きました",
            'type' => 'new_request',
            'request_id' => $this->request->id,
            'url' => route('admin.attendance-requests.index'),
        ];
    }
}