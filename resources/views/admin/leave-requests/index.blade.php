@extends('layouts.app')

@section('title', '休暇承認')

@section('content')
<div class="card">
    <h1 class="card-title">休暇承認 - 承認待ち一覧</h1>

    @if (session('success'))
        <div style="background: #d1fae5; color: #047857; padding: 10px 14px; border-radius: 6px; margin-bottom: 16px; font-size: 14px;">
            {{ session('success') }}
        </div>
    @endif
    @if (session('error'))
        <div style="background: #fee2e2; color: #991b1b; padding: 10px 14px; border-radius: 6px; margin-bottom: 16px; font-size: 14px;">
            {{ session('error') }}
        </div>
    @endif

    @forelse ($requests as $request)
        <div style="border-bottom: 1px solid #eee; padding: 16px 0;">
            <div style="display: grid; grid-template-columns: 120px 1fr 220px; gap: 16px; margin-bottom: 12px; align-items: start;">
                <div style="font-weight: bold;">{{ $request->user->name }}</div>
                <div>
                    <div style="font-weight: bold;">{{ $request->type->label() }}</div>
                    <div style="color: #555; font-size: 13px; margin-top: 4px;">{{ $request->reason }}</div>
                </div>
                <div style="font-family: monospace; color: #666; font-size: 13px;">
                    {{ $request->start_date->format('Y-m-d') }} 〜 {{ $request->end_date->format('Y-m-d') }}
                </div>
            </div>

            <div style="display: flex; gap: 8px; align-items: center;">
                <form action="{{ route('admin.leave-requests.approve', $request) }}" method="POST"
                      onsubmit="return confirm('この申請を承認しますか?');">
                    @csrf
                    @method('PUT')
                    <button type="submit" style="padding: 6px 16px; background: #10b981; color: white; border: none; border-radius: 6px; font-size: 13px; cursor: pointer;">承認</button>
                </form>

                <form action="{{ route('admin.leave-requests.reject', $request) }}" method="POST" style="display: flex; gap: 8px;"
                      onsubmit="return confirm('この申請を却下しますか?');">
                    @csrf
                    @method('PUT')
                    <input type="text" name="admin_comment" placeholder="却下理由(任意)" style="padding: 6px 12px; border: 1px solid #ddd; border-radius: 6px; font-size: 13px; width: 200px;">
                    <button type="submit" style="padding: 6px 16px; background: #e74c3c; color: white; border: none; border-radius: 6px; font-size: 13px; cursor: pointer;">却下</button>
                </form>
            </div>
        </div>
    @empty
        <p style="color: #888; text-align: center; padding: 40px 0;">承認待ちの休暇申請はありません</p>
    @endforelse
</div>
@endsection
