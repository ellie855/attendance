@extends('layouts.app')

@section('title', '通知')

@section('content')
<div class="card">
    <h1 class="card-title">通知</h1>

    @forelse ($notifications as $notification)
        <a href="{{ route('notifications.read', $notification->id) }}"
           style="display: flex; align-items: center; gap: 16px; padding: 12px 16px; background: {{ $notification->read_at ? 'white' : '#eff6ff' }}; border: 1px solid {{ $notification->read_at ? '#eee' : '#93c5fd' }}; border-radius: 8px; margin-bottom: 6px; text-decoration: none; color: inherit; transition: all 0.15s;">
            @if (!$notification->read_at)
                <span style="width: 8px; height: 8px; background: #06c; border-radius: 50%; flex-shrink: 0;"></span>
            @else
                <span style="width: 8px; height: 8px; flex-shrink: 0;"></span>
            @endif

            <div style="flex: 1;">
                <div style="font-size: 14px; {{ $notification->read_at ? '' : 'font-weight: bold;' }}">
                    {{ $notification->data['message'] ?? '(通知内容なし)' }}
                </div>
                <div style="font-size: 11px; color: #888; margin-top: 4px;">
                    {{ $notification->created_at->diffForHumans() }}
                </div>
            </div>

            <span style="color: #aaa; font-size: 18px;">›</span>
        </a>
    @empty
        <p style="margin-top: 20px; color: #888; text-align: center; padding: 40px 0;">
            通知はありません。
        </p>
    @endforelse

    <div style="margin-top: 20px;">
        {{ $notifications->links() }}
    </div>
</div>
@endsection
