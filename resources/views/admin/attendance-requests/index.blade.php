@extends('layouts.app')

@section('title', '承認待ち申請')

@section('content')

@if (session('success'))
    <div id="flash" class="flash-success" style="background: #d4edda; color: #155724; padding: 12px; margin-bottom: 16px; border-radius: 6px; text-align: center; transition: opacity 0.5s;">
        ✓ {{ session('success') }}
    </div>
@endif

@if (session('error'))
    <div style="background: #f8d7da; color: #721c24; padding: 12px; margin-bottom: 16px; border-radius: 6px; text-align: center;">
        ⚠ {{ session('error') }}
    </div>
@endif

<div class="card">
    <h1 class="card-title">承認待ち申請</h1>

    @forelse ($requests as $request)
        <div style="border: 1px solid #eee; border-radius: 8px; padding: 16px; margin-top: 16px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                <div style="font-weight: bold; font-size: 15px;">
                    {{ $request->user->name }}
                    <span style="color: #666; font-size: 12px; font-weight: normal; margin-left: 8px;">
                        {{ $request->user->email }}
                    </span>
                </div>
                <span style="color: #888; font-size: 11px;">申請: {{ $request->created_at->format('Y-m-d H:i') }}</span>
            </div>

            <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 8px;">
                @if ($request->type?->value === 'add')
                    <span style="background:#dbeafe; color:#1e40af; padding:2px 8px; border-radius:4px; font-size:12px;">追加</span>
                    <span style="color: #666;">{{ $request->clock_type?->label() }}</span>
                    <span style="color: #666;">→</span>
                    <span style="font-weight: bold;">{{ $request->new_time->format('Y-m-d H:i') }}</span>
                @else
                    <span style="background:#fef3c7; color:#92400e; padding:2px 8px; border-radius:4px; font-size:12px;">修正</span>
                    <span style="color: #666;">{{ $request->targetAttendance?->created_at->format('Y-m-d H:i') ?? '?' }}</span>
                    <span style="color: #666;">{{ $request->targetAttendance?->type?->label() ?? '(不明)' }}</span>
                    <span style="color: #666;">→</span>
                    <span style="font-weight: bold;">{{ $request->new_time->format('Y-m-d H:i') }}</span>
                @endif
            </div>

            <div style="color: #555; font-size: 13px; margin-bottom: 12px;">
                理由: {{ $request->reason }}
            </div>

            <div style="display: flex; gap: 8px;">
                <form action="{{ route('admin.attendance-requests.approve', $request) }}" method="POST"
                      onsubmit="return confirm('この申請を承認しますか?');" style="display: inline;">
                    @csrf
                    @method('PUT')
                    <button type="submit" style="padding: 6px 16px; background: #10b981; color: white; border: none; border-radius: 4px; font-size: 13px; cursor: pointer;">
                        ✓ 承認
                    </button>
                </form>

                <form action="{{ route('admin.attendance-requests.reject', $request) }}" method="POST"
                      onsubmit="return confirm('この申請を却下しますか?');" style="display: inline;">
                    @csrf
                    @method('PUT')
                    <input type="text" name="admin_comment" placeholder="却下理由(任意)"
                           style="padding: 6px 8px; font-size: 12px; border: 1px solid #ccc; border-radius: 4px; width: 200px;">
                    <button type="submit" style="padding: 6px 16px; background: #e74c3c; color: white; border: none; border-radius: 4px; font-size: 13px; cursor: pointer;">
                        ✗ 却下
                    </button>
                </form>
            </div>
        </div>
    @empty
        <p style="margin-top: 20px; color: #888; text-align: center; padding: 40px 0;">
            承認待ちの申請はありません。
        </p>
    @endforelse
</div>

<script>
    const flash = document.getElementById('flash');
    if (flash) {
        setTimeout(() => flash.style.opacity = '0', 1000);
        setTimeout(() => flash.remove(), 1500);
    }
</script>

@endsection