@extends('layouts.app')

@section('title', '勤怠修正申請 - 編集')

@section('content')
<div class="card">
    <h1 class="card-title">勤怠修正申請 - 編集</h1>

    <p style="margin-top: 12px;">
        <a href="{{ route('attendance-requests.index') }}">← 一覧に戻る</a>
    </p>

    <form action="{{ route('attendance-requests.update', $attendanceRequest) }}" method="POST" style="margin-top: 20px;">
        @csrf
        @method('PUT')

        <label for="target_attendance_id" style="display: block; margin-bottom: 6px; font-weight: bold;">
            修正したい打刻を選択
        </label>
        <select id="target_attendance_id" name="target_attendance_id" style="width: 100%; padding: 8px; margin-bottom: 4px;">
            @forelse ($attendances as $attendance)
                <option value="{{ $attendance->id }}"
                    @selected(old('target_attendance_id', $attendanceRequest->target_attendance_id) == $attendance->id)>
                    {{ $attendance->created_at->format('Y-m-d H:i') }} - {{ $attendance->type->label() }}
                </option>
            @empty
                <option value="" disabled>過去14日間の打刻がありません</option>
            @endforelse
        </select>
        @error('target_attendance_id')
            <div style="color: #c00; font-size: 13px; margin-bottom: 12px;">{{ $message }}</div>
        @enderror

        <label for="new_time" style="display: block; margin-top: 16px; margin-bottom: 6px; font-weight: bold;">
            希望の時刻
        </label>
        <input type="datetime-local" id="new_time" name="new_time"
               value="{{ old('new_time', $attendanceRequest->new_time->format('Y-m-d\TH:i')) }}"
               style="width: 100%; padding: 8px; margin-bottom: 4px;">
        @error('new_time')
            <div style="color: #c00; font-size: 13px; margin-bottom: 12px;">{{ $message }}</div>
        @enderror

        <label for="reason" style="display: block; margin-top: 16px; margin-bottom: 6px; font-weight: bold;">
            修正理由
        </label>
        <textarea id="reason" name="reason" rows="4"
                  style="width: 100%; padding: 8px; margin-bottom: 4px;">{{ old('reason', $attendanceRequest->reason) }}</textarea>
        @error('reason')
            <div style="color: #c00; font-size: 13px; margin-bottom: 12px;">{{ $message }}</div>
        @enderror

        <div style="text-align: right; margin-top: 20px;">
            <button type="submit" class="btn-in" style="padding: 12px 32px; font-size: 15px;">更新する</button>
        </div>
    </form>
</div>
@endsection