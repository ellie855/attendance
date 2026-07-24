@extends('layouts.app')

@section('title', '勤怠修正申請')

@section('content')

@if (session('success'))
    <div id="flash" class="flash-success" style="background: #d4edda; color: #155724; padding: 12px; margin-bottom: 16px; border-radius: 6px; text-align: center; transition: opacity 0.5s;">
        ✓ {{ session('success') }}
    </div>
@endif

<div class="card">
    <div style="display: flex; justify-content: space-between; align-items: center;">
        <h1 class="card-title">勤怠修正申請</h1>
        <a href="{{ route('attendance-requests.create') }}" class="btn-link btn-in" style="padding: 10px 24px; font-size: 14px;">新規申請</a>
    </div>

<div class="request-list" style="margin-top: 16px;">
        @forelse ($requests as $request)
            <a href="{{ route('attendance-requests.show', $request) }}"
               class="request-item"
               style="display: grid; grid-template-columns: 90px 100px 220px 1fr auto; gap: 16px; align-items: center; padding: 12px 16px; background: white; border: 1px solid #eee; border-radius: 8px; margin-bottom: 6px; text-decoration: none; color: inherit; transition: all 0.15s;">
                <span>
                    @if ($request->status === 'pending')
                        <span style="background: #fef3c7; color: #92400e; padding: 3px 10px; border-radius: 12px; font-size: 12px;">承認待ち</span>
                    @elseif ($request->status === 'approved')
                        <span style="background: #d1fae5; color: #047857; padding: 3px 10px; border-radius: 12px; font-size: 12px;">承認済</span>
                    @elseif ($request->status === 'rejected')
                        <span style="background: #fee2e2; color: #991b1b; padding: 3px 10px; border-radius: 12px; font-size: 12px;">却下</span>
                    @endif
                </span>
                <span style="font-size: 13px; font-weight: bold;">{{ $request->targetAttendance->type->label() }}</span>
                <span style="font-family: monospace; font-size: 13px; color: #555;">
                    {{ $request->targetAttendance->created_at->format('m-d H:i') }} → {{ $request->new_time->format('m-d H:i') }}
                </span>
                <span style="font-size: 12px; color: #666; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                    理由: {{ $request->reason }}
                </span>
                <span style="color: #aaa; font-size: 18px;">›</span>
            </a>
        @empty
            <p style="margin-top: 20px; color: #888; text-align: center; padding: 40px 0;">
                まだ申請はありません。
            </p>
        @endforelse
    </div>

    <style>
        .request-item:hover {
            background: #f8fafc !important;
            border-color: #06c !important;
            transform: translateX(2px);
        }
    </style>
</div>

<script>
    const flash = document.getElementById('flash');
    if (flash) {
        setTimeout(() => flash.style.opacity = '0', 1000);
        setTimeout(() => flash.remove(), 1500);
    }
</script>

@endsection