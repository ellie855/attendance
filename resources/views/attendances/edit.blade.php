@extends('layouts.app')

@section('title', '勤怠修正申請')

@section('content')
    <h1>勤怠修正申請</h1>

    <p><a href="{{ route('attendances.index') }}">← 戻る</a></p>

    <form action="{{ route('attendances.update', $attendance) }}" method="POST" class="card" style="display:block;">
        @csrf
        @method('PUT')

        <label for="type">種別</label>
        <select id="type" name="type">
            <option value="clock_in"  @selected(old('type', $attendance->type->value) === 'clock_in')>出勤</option>
            <option value="clock_out" @selected(old('type', $attendance->type->value) === 'clock_out')>退勤</option>
        </select>
        @error('type') <div class="error">{{ $message }}</div> @enderror

        <label for="created_at">打刻日時</label>
        <input type="datetime-local" id="created_at" name="created_at"
               value="{{ old('created_at', $attendance->created_at->format('Y-m-d\TH:i:s')) }}">
        @error('created_at') <div class="error">{{ $message }}</div> @enderror

        <p style="margin-top: 20px;">
            <button type="submit" class="btn-in">申請する</button>
        </p>
    </form>

    <form action="{{ route('attendances.destroy', $attendance) }}" method="POST"
          onsubmit="return confirm('この打刻を削除します。よろしいですか?');" style="margin-top: 16px;">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn-danger">この打刻を削除</button>
    </form>
@endsection