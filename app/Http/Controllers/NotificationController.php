<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NotificationController extends Controller
{
    // 通知一覧ページを表示
    public function index()
    {
        // ログイン中のユーザーの通知を、新しい順に２０件ずつ取得
        $notifications = auth()->user()
            ->notifications()
            ->orderBy('created_at', 'desc')
            ->paginate(20);

            return view('notifications.index', [
                'notifications' => $notifications,
            ]);
    }

    // 通知をクリックされた時：「既読」にしてリンク先に飛ばす
    public function markAsRead($id)
    {
        //　自分の通知の中から探す
        $notification = auth()->user()->notifications()->findOrFail($id);
        $notification->markAsRead();

        // 通知内のurlに飛ばす
        $url = $notification->data['url'] ?? route('notifications.index');
        return redirect($url);
    }
}
