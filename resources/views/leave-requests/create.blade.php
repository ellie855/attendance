@extends('layouts.app')

@section('title', '休暇申請 - 新規')

@section('content')
<div class="card">
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
    <h1 class="card-title" style="margin: 0; border: none; padding: 0;">新規休暇申請</h1>
    <a href="{{ route('leave-requests.index') }}" style="color: #06c; text-decoration: none;">←一覧に戻る</a>
</div>

<form method="POST" action="{{ route('leave-requests.store') }}">
    @csrf

    <div style="margin-bottom: 16px;">
        <label style="display: block; font-size: 13px; color: #555; margin-bottom: 6px;">休暇の種類</label>
        <select name="type" required
            style="width: 100%; padding: 8px 12px; border: 1px solid #ddd; border-radius: 6px; font-size: 14px;">
            <option value="">-- 選択してください --</option>
            @foreach ($types as $type)
                <option value="{{ $type->value }}" {{ old('type') === $type->value ? 'selected' : '' }}>
                    {{ $type->label() }}
                </option>
            @endforeach
        </select>
        @error('type')
            <p style="color: #e74c3c; font-size: 12px; margin-top: 4px;">{{ $message }}</p>
        @enderror
    </div>

    <div style="display: flex; gap: 16px; margin-bottom: 16px;">
        <div style="flex: 1;">
            <label style="display: block; font-size: 13px; color: #555; margin-bottom: 6px;">開始日</label>
            <input type="date" name="start_date" value="{{ old('start_date') }}" required
                style="width: 100%; padding: 8px 12px; border: 1px solid #ddd; border-radius: 6px; font-size: 14px;">
            @error('start_date')
                <p style="color: #e74c3c; font-size: 12px; margin-top: 4px;">{{ $message }}</p>
            @enderror
        </div>
        <div style="flex: 1;">
            <label style="display: block; font-size: 13px; color: #555; margin-bottom: 6px;">終了日</label>
            <input type="date" name="end_date" value="{{ old('end_date') }}" required
                style="width: 100%; padding: 8px 12px; border: 1px solid #ddd; border-radius: 6px; font-size: 14px;">
            @error('end_date')
                <p style="color: #e74c3c; font-size: 12px; margin-top: 4px;">{{ $message }}</p>            @enderror
        </div>
    </div>

    <div style="margin-bottom: 20px;">
        <label style="display: block; font-size: 13px; color: #555; margin-bottom: 6px;">申請理由</label>
        <textarea name="reason" rows="4" required
            style="width: 100%; padding: 8px 12px; border: 1px solid #ddd; border-radius: 6px; font-size: 14px; font-family: inherit; resize: vertical;">{{ old('reason') }}</textarea>
        @error('reason')
            <p style="color: #e74c3c; font-size: 12px; margin-top: 4px;">{{ $message }}</p>
        @enderror
    </div>

    <button type="submit"
        style="padding: 10px 24px; background: #06c; color: white; border: none; border-radius: 6px; font-size: 14px; cursor: pointer;">申請する</button>
</form>
</div>
@endsection