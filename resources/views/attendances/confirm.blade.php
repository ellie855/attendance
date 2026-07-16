@extends('layouts.app')

@section('title', $type === 'clock_in' ? '出勤確認' : '退勤確認')

@section('content')
    @php
        $label    = $type === 'clock_in' ? '出勤' : '退勤';
        $routeName = $type === 'clock_in' ? 'attendances.clock-in' : 'attendances.clock-out';
        $btnClass = $type === 'clock_in' ? 'btn-in' : 'btn-out';
    @endphp

    <div class="confirm-box">
        <h1>{{ $label }}しますか?</h1>
        @if ($warning)
            <div class="warning-box">
                <i class="bi bi-exclamation-triangle-fill"></i>
                {{ $warning }}
            </div>
        @endif

        <p class="confirm-time" id="confirm-time">--:--:--</p>

        <div class="confirm-actions">
            <form action="{{ route($routeName) }}" method="POST" style="display:inline;">
                @csrf
                <button type="submit" class="{{ $btnClass }}">はい、{{ $label }}します</button>
            </form>
            <a href="{{ route('attendances.index') }}" class="btn-cancel">キャンセル</a>
        </div>
    </div>

    <script>
        function updateConfirmTime() {
            const now = new Date();
            const pad = (n) => String(n).padStart(2, '0');
            document.getElementById('confirm-time').textContent =
                now.getFullYear() + '-' + pad(now.getMonth()+1) + '-' + pad(now.getDate()) + ' ' +
                pad(now.getHours()) + ':' + pad(now.getMinutes()) + ':' + pad(now.getSeconds());
        }
        updateConfirmTime();
        setInterval(updateConfirmTime, 1000);
    </script>
@endsection