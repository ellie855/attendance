@extends('layouts.app')

@section('title', '休暇申請 - 詳細')

@section('content')
<div class="card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h1 class="card-title" style="margin: 0; border: none; padding: 0;">休暇申請 詳細</h1>
        <a href="{{ route('leave-requests.index') }}" style="color: #06c; text-decoration: none;">←一覧に戻る</a>
    </div>
    
    <div style="margin-bottom: 20px;">
        @if ($leaveRequest->status === 'pending')
            <span style="background: #fef3c7; color: #92400e; padding: 4px 12px; border-radius: 12px; font-size: 13px;">承認待ち</span>
        @elseif ($leaveRequest->status === 'approved')
            <span style="background: #d1fae5; color: #047857; padding: 4px 12px; border-radius: 12px; font-size: 13px;">承認済</span>
        @elseif ($leaveRequest->status === 'rejected')
            <span style="background: #fee2e2; color: #991b1b; padding: 4px 12px; border-radius: 12px; font-size: 13px;">却下</span>
        @endif
    </div>

    <div style="display: grid; grid-template-columns: 120px 1fr; gap: 12px 20px; font-size: 14px; margin-bottom: 24px">
        <div style="color: #888;">種類</div><div>{{ $leaveRequest->type->label() }}</div>
        <div style="color: #888;">期間</div><div style="font-family: monospace;">{{ $leaveRequest->start_date->format('Y-m-d') }} ～ {{ $leaveRequest->end_date->format('Y-m-d') }}</div>
        <div style="color: #888;">理由</div><div>{{ $leaveRequest->reason }}</div>
        <div style="color: #888;">申請日時</div><div style="font-family: monospace; color: #666;">{{ $leaveRequest->created_at->format('Y-m-d H:i') }}</div>
    @if ($leaveRequest->admin_comment)
        <div style="color: #888;">管理者コメント</div><div style="color: #991b1b;">{{ $leaveRequest->admin_comment }}</div>
    @endif
    </div>

    @can('update', $leaveRequest)
        <div style="display: flex; gap: 8px; padding-top: 16px; border-top: 1px solid #eee;">
            <a href="{{ route('leave-requests.edit', $leaveRequest) }}"
               style="padding: 8px 20px; background: #06c; color: white; text-decoration: none; border-radius: 6px; font-size: 14px;">修正する</a>
            <form action="{{ route('leave-requests.destroy', $leaveRequest) }}" method="POST"
                  onsubmit="return confirm('この申請を取り下げますか？');">
                @csrf
                @method('DELETE')
                <button type="submit" style="padding: 8px 20px; background: #e74c3c; color: white; border: none; border-radius: 6px; font-size: 14px; cursor: pointer;">取り下げる</button>
            </form>
        </div>
    @endcan
</div>
@endsection
