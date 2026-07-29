@extends('layouts.app')

@section('title', '休暇申請')

@section('content')
<div class="card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h1 class="card-title" style="margin: 0; border: none; padding: 0;">休暇申請</h1>
        <a href="{{ route('leave-requests.create') }}"
           style="padding: 8px 16px; background: #06c; color: white; text-decoration: none; border-radius: 6px; font-size: 14px;">+ 新規申請</a>
    </div>

    @if (session('success'))
        <div style="background: #d1fae5; color: #047857; padding: 10px 14px; border-radius: 6px; margin-bottom: 16px; font-size: 14px;">
            {{ session('success') }}
        </div>
    @endif

    @forelse ($requests as $request)
            <a href="{{ route('leave-requests.show', $request) }}" onmouseover="this.style.background='#f0f7ff'" onmouseout="this.style.background='transparent'" style="display: grid; grid-template-columns: 100px 1fr 200px 100px; gap: 12px; align-items: center; padding: 12px 16px; border-bottom: 1px solid #eee; font-size: 14px; text-decoration: none; color: inherit; cursor: pointer;">
            <div style="font-weight: bold;">{{ $request->type->label() }}</div>
            <div style="color: #555;">{{ Str::limit($request->reason, 40) }}</div>
            <div style="font-family: monospace; color: #666;">
                {{ $request->start_date->format('Y-m-d') }} ~ {{ $request->end_date->format('Y-m-d') }}
            </div>
            <div>
                @if ($request->status === 'pending')
                    <span style="background: #fef3c7; color: #92400e; padding: 3px 10px; border-radius: 10px; font-size: 12px;">承認待ち</span>
                @elseif ($request->status === 'approved')
                    <span style="background: #d1fae5; color: #047857; padding: 3px 10px; border-radius: 10px; font-size: 12px;">承認済</span>
                @elseif ($request->status === 'rejected')
                    <span style="background: #fee2e2; color: #991b1b; padding: 3px 10px; border-radius: 10px; font-size: 12px;">却下</span>
                @endif
            </div>
        </a>
    @empty
        <p style="color: #888; text-align: center; padding: 40px 0;">まだ休暇申請はありません</p>
    @endforelse
</div>
@endsection
