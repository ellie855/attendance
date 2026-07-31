@extends('layouts.app')

@section('title', '勤怠修正申請 - 詳細')

@section('content')
<div class="card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h1 class="card-title" style="margin: 0; border: none; padding: 0;">申請詳細</h1>
        <a href="{{ route('attendance-requests.index') }}" style="color: #06c; text-decoration: none;">← 一覧に戻る</a>
    </div>

    <div style="margin-bottom: 20px;">
        @if ($attendanceRequest->status === 'pending')
            <span style="background: #fef3c7; color: #92400e; padding: 4px 12px; border-radius: 12px; font-size: 13px;">承認待ち</span>
        @elseif ($attendanceRequest->status === 'approved')
            <span style="background: #d1fae5; color: #047857; padding: 4px 12px; border-radius: 12px; font-size: 13px;">承認済</span>
        @elseif ($attendanceRequest->status === 'rejected')
            <span style="background: #fee2e2; color: #991b1b; padding: 4px 12px; border-radius: 12px; font-size: 13px;">却下</span>
        @endif
    </div>

    <div style="display: grid; grid-template-columns: 120px 1fr; gap: 12px 20px; font-size: 14px; margin-bottom: 24px;">
        <div style="color: #888;">申請種別</div>
        <div>
            @if ($attendanceRequest->type?->value === 'add')
                <span style="background:#dbeafe; color:#1e40af; padding:2px 8px; border-radius:4px; font-size:12px;">追加</span>
                打刻追加
            @else
                <span style="background:#fef3c7; color:#92400e; padding:2px 8px; border-radius:4px; font-size:12px;">修正</span>    
                既存打刻の修正
            @endif
        </div>

        <div style="color: #888;">打刻種類</div>
        <div>
            @if ($attendanceRequest->type?->value === 'add')
                {{ $attendanceRequest->clock_type?->label() }}
            @else
                {{ $attendanceRequest->targetAttendance?->type?->label() ?? '(打刻不明)' }}
            @endif
        </div>

        @if ($attendanceRequest->type?->value !== 'add')
            <div style="color: #888;">元の時刻</div>
            <div style="font-family: monospace;">{{ $attendanceRequest->targetAttendance?->created_at->format('Y-m-d H:i') ?? '?' }}</div>
        @endif

        <div style="color: #888;">{{ $attendanceRequest->type?->value === 'add' ? '追加する時刻' : '修正希望' }}</div>
        <div style="font-family: monospace; font-weight: bold;">{{$attendanceRequest->new_time->format('Y-m-d H:i') }}</div>

        <div style="color: #888;">理由</div><div>{{ $attendanceRequest->reason }}</div>
        <div style="color: #888;">申請日時</div><div style="font-family: monospace; color: #666;">{{ $attendanceRequest->created_at->format('Y-m-d H:i') }}</div>
        
        @if ($attendanceRequest->admin_comment)
            <div style="color: #888;">管理者コメント</div><div style="color: #991b1b;">{{ $attendanceRequest->admin_comment }}</div>
        @endif
    </div>

    @can('update', $attendanceRequest)
        <div style="display: flex; gap: 8px; padding-top: 16px; border-top: 1px solid #eee;">
            <a href="{{ route('attendance-requests.edit', $attendanceRequest) }}"
               style="padding: 8px 20px; background: #06c; color: white; text-decoration: none; border-radius: 6px; font-size: 14px;">修正する</a>
            <form action="{{ route('attendance-requests.destroy', $attendanceRequest) }}" method="POST"
                  onsubmit="return confirm('この申請を取り下げますか?');">
                @csrf
                @method('DELETE')
                <button type="submit" style="padding: 8px 20px; background: #e74c3c; color: white; border: none; border-radius: 6px; font-size: 14px; cursor: pointer;">取り下げる</button>
            </form>
        </div>
    @endcan
</div>
@endsection