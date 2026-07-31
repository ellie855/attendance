@extends('layouts.app')

@section('title', '勤怠修正申請 - 新規')

@section('content')
<div class="card">
    <h1 class="card-title">勤怠修正申請 - 新規</h1>

    <p style="margin-top: 12px;">
        <a href="{{ route('attendance-requests.index') }}">← 一覧に戻る</a>
    </p>

    <form action="{{ route('attendance-requests.store') }}" method="POST" style="margin-top: 20px;">
        @csrf

        {{-- 申請種別を選ぶラジオボタン --}}
<label style="display: block; margin-bottom: 6px; font-weight: bold;">申請種別</label>
<div style="display: flex; gap: 20px; margin-bottom: 16px;">
    @foreach ($requestTypes as $rt)
        <label style="display: flex; align-items: center; gap: 6px; cursor: pointer;">
            <input type="radio" name="type" value="{{ $rt->value }}"
                   {{ old('type', 'modify') === $rt->value ? 'checked' : '' }}
                   onchange="toggleTypeFields()">
            {{ $rt->label() }}
        </label>
    @endforeach
</div>
@error('type')
    <div style="color: #c00; font-size: 13px; margin-bottom: 12px;">{{ $message }}</div>
@enderror

{{-- ▼ modify 用: 既存打刻を選ぶ --}}
<div id="modifyFields">
    <label for="target_attendance_id" style="display: block; margin-bottom: 6px; font-weight: bold;">
        修正したい打刻を選択
    </label>
    <select id="target_attendance_id" name="target_attendance_id" style="width: 100%; padding: 8px; margin-bottom: 4px;">
        @forelse ($attendances as $attendance)
            <option value="{{ $attendance->id }}">
                {{ $attendance->created_at->format('Y-m-d H:i') }} - {{ $attendance->type->label() }}
            </option>
        @empty
            <option value="" disabled>過去14日間の打刻がありません</option>
        @endforelse
    </select>
    @error('target_attendance_id')
        <div style="color: #c00; font-size: 13px; margin-bottom: 12px;">{{ $message }}</div>
    @enderror
</div>

{{-- ▼ add 用: 打刻種類を選ぶ --}}
<div id="addFields" style="display: none;">
    <label for="clock_type" style="display: block; margin-bottom: 6px; font-weight: bold;">
        追加する打刻の種類
    </label>
    <select id="clock_type" name="clock_type" style="width: 100%; padding: 8px; margin-bottom: 4px;">
        @foreach ($clockTypes as $ct)
            <option value="{{ $ct->value }}" {{ old('clock_type') === $ct->value ? 'selected' : '' }}>
                {{ $ct->label() }}
            </option>
        @endforeach
    </select>
    @error('clock_type')
        <div style="color: #c00; font-size: 13px; margin-bottom: 12px;">{{ $message }}</div>
    @enderror
</div>


        <label for="new_time" style="display: block; margin-top: 16px; margin-bottom: 6px; font-weight: bold;">
            希望の時刻
        </label>
        <input type="datetime-local" id="new_time" name="new_time"
               value="{{ old('new_time') }}"
               style="width: 100%; padding: 8px; margin-bottom: 4px;">
        @error('new_time')
            <div style="color: #c00; font-size: 13px; margin-bottom: 12px;">{{ $message }}</div>
        @enderror

        <label for="reason" style="display: block; margin-top: 16px; margin-bottom: 6px; font-weight: bold;">
            修正理由
        </label>
        <textarea id="reason" name="reason" rows="4"
                  placeholder="例: 打刻を忘れていたため、実際の退勤時刻に修正したい"
                  style="width: 100%; padding: 8px; margin-bottom: 4px;">{{ old('reason') }}</textarea>
        @error('reason')
            <div style="color: #c00; font-size: 13px; margin-bottom: 12px;">{{ $message }}</div>
        @enderror

        <div style="text-align: right; margin-top: 20px;">
            <button type="submit" class="btn-in" style="padding: 12px 32px; font-size: 15px;">申請を送信</button>
        </div>
    </form>
</div>
<script>
    function toggleTypeFields() {
        const selected = document.querySelector('input[name="type"]:checked').value;
        document.getElementById('modifyFields').style.display = selected === 'modify' ? 'block' : 'none';
        document.getElementById('addFields').style.display = selected === 'add' ? 'block' : 'none';
    }
    // ページ読み込み時にも実行(old値対応)
    toggleTypeFields();
</script>
@endsection