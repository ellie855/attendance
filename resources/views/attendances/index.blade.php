@extends('layouts.app')

@section('title', '勤怠管理')

@section('content')
@if (session('success'))
    <div id="flash" class="flash-success" style="background: #d4edda; color: #155724; padding: 12px; margin-bottom: 16px; border-radius: 6px; text-align: center; transition: opacity 0.5s;">
        ✓ {{ session('success') }}
    </div>
@endif
<div class="dashboard-grid">
    <div class="dashboard-left">
        <div class="card">
            <h1 class="card-title">勤怠管理</h1>

            <div class="card-main">
                <div class="left">
                    <div id="date" class="clock-date"></div>
                    <div id="clock" class="clock-time">--:--:--</div>
                </div>

                <div class="right">
                    <a href="{{ route('attendance-requests.index') }}" class="info-box">勤怠修正申請</a>
                    <div class="info-box status-{{ $status === '勤務中' ? 'on' : ($status === '退勤済' ? 'off' : 'none') }}">
                        {{ $status }}
                    </div>
                    <div class="info-box user-box">{{ $userName }}</div>
                </div>
            </div>

            <div class="actions">
                @if ($status === '未出勤')
                    <a href="{{ route('attendances.confirm-in') }}" class="btn-link btn-in">出勤</a>
                @elseif ($status === '勤務中')    
                    <a href="{{ route('attendances.confirm-out') }}" class="btn-link btn-out">退勤</a>
                    <form action="{{ route('attendances.break-start') }}" method="POST" style="display:inline;">
                        @csrf
                        <button type="submit" class="btn-link btn-out">休憩に入る</button>
                    </form>
                @elseif ($status === '休憩中')
                    <form action="{{ route('attendances.break-end') }}" method="POST" style="display:inline;">
                    @csrf 
                    <button type="submit" class="btn-link btn-in">休憩を終える</button>
                </form>
            @else
                <p>お疲れさまでした</p>
                <a href="{{ route('attendances.confirm-in') }}" class="btn-link btn-in">再出勤</a>
            @endif
            </div>
        </div>

        <div class="card" style="margin-top: 16px;">
            <h2 class="card-title" style="display: flex; justify-content: space-between; align-items: center;">
                今日の作業内容
                <span id="saveStatus" class="save-status saved" style="font-size: 13px; font-weight: normal; padding: 4px 10px; border-radius: 12px;">
                    <span id="statusIcon">✓</span>
                    <span id="statusText">保存済み</span>
                </span>
            </h2>
            <form id="noteForm" action="{{ route('daily-notes.store') }}" method="POST" style="margin-top: 12px;">
                @csrf
                <textarea id="noteEditor" name="note" rows="3" style="width: 100%; padding: 8px;"
                        placeholder="今日やったことを書いてください">{{ old('note', $todayNote?->note) }}</textarea>
                <div style="text-align: right; margin-top: 8px;">
                    <button type="submit" class="btn-link btn-in">保存</button>
                </div>
            </form>
        </div>

        <div class="summary-grid">
            <div class="summary-card">
                <div class="icon"><i class="bi bi-clock-fill"></i></div>
                <div class="label">今月の総勤務時間</div>
                <div class="value">{{ $summary['total_hours'] ?? 0 }}<span class="unit">時間</span></div>
            </div>
            <div class="summary-card">
                <div class="icon"><i class="bi bi-calendar-check-fill"></i></div>
                <div class="label">出勤日数</div>
                <div class="value">{{ $summary['days'] ?? 0 }}<span class="unit">日</span></div>
            </div>
            <div class="summary-card">
                <div class="icon"><i class="bi bi-graph-up"></i></div>
                <div class="label">平均勤務時間</div>
                <div class="value">{{ $summary['avg_hours'] ?? 0 }}<span class="unit">時間</span></div>
            </div>
            <div class="summary-card">
                <div class="icon"><i class="bi bi-fire"></i></div>
                <div class="label">連続出勤</div>
                <div class="value">{{ $summary['streak'] ?? 0 }}<span class="unit">日</span></div>
            </div>
        </div>
    </div>

    <div class="dashboard-right">
        <h2 id="history">履歴</h2>
        <div class="history-list" style="max-height: 600px;">
            @forelse ($attendances as $attendance)
                <div class="log-row">
                    <span class="log-time">{{ $attendance->created_at->format('Y-m-d H:i') }}</span>
                    @if (auth()->user()->isAdmin())
                        <span class="log-user">{{ $attendance->user?->name ?? '退会済' }}</span>
                    @endif
                    <span class="log-type {{ $attendance->type->value }}">
                        <i class="bi bi-circle-fill"></i>
                        {{ $attendance->type->label() }}
                    </span>
                    @if (isset($durations[$attendance->id]))
                        <span class="log-duration">{{ $durations[$attendance->id] }}</span>
                    @endif
                    @if (in_array($attendance->id, $ongoingClockIns))
                        <span class="log-elapsed" data-since="{{ $attendance->created_at->getTimestamp() }}000">--:--:--</span>
                    @endif
                    @can('update', $attendance)
                        <a href="{{ route('attendances.edit', $attendance) }}" class="log-edit">編集</a>
                    @endcan
                </div>
            @empty
                <p>まだ打刻履歴がありません。</p>
            @endforelse
        </div>
    </div>
</div>

<script>
    function updateClock() {
        const now = new Date();
        const pad = (n) => String(n).padStart(2, '0');
        const time = pad(now.getHours()) + ':' + pad(now.getMinutes());
        const days = ['日', '月', '火', '水', '木', '金', '土'];
        const date = now.getFullYear() + '-' + pad(now.getMonth() + 1) + '-' + pad(now.getDate()) + ' (' + days[now.getDay()] + ')';
        document.getElementById('clock').innerHTML = time;
        document.getElementById('date').textContent = date;
    }
    updateClock();
    setInterval(updateClock, 1000);

    function updateElapsed() {
        document.querySelectorAll('.log-elapsed').forEach(el => {
            const sinceMs = parseInt(el.dataset.since);
            const elapsed = Math.floor((Date.now() - sinceMs) / 1000);
            const h = Math.floor(elapsed / 3600);
            const m = Math.floor((elapsed % 3600) / 60);
            el.textContent = h + '時間' + m + '分';
        });
    }
    updateElapsed();
    setInterval(updateElapsed, 1000);

    //保存状態バッジ
    const editor = document.getElementById('noteEditor');
    const status = document.getElementById('saveStatus');
    const statusIcon = document.getElementById('statusIcon');
    const statusText = document.getElementById('statusText');
    let savedContent = editor.value;

    editor.addEventListener('input', () => {
        if (editor.value === savedContent) {
            status.className = 'save-status saved';
            statusIcon.textContent = '✓';
            statusText.textContent = '保存済み';
        } else {
            status.className = 'save-status dirty';
            statusIcon.textContent = '●';
            statusText.textContent = '未保存の変更あり';
        }
    });

//フラッシュメッセージ自動消去
const flash = document.getElementById('flash');
if (flash) {
    setTimeout(() => flash.style.opacity = '0', 1000);
    setTimeout(() => flash.remove(), 1500);
}
</script>
@endsection
