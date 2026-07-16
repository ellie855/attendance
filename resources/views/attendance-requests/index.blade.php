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

    @forelse ($requests as $request)
        <div class="log-row" style="align-items: flex-start;">
            <div style="flex: 1;">
                <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 4px;">
                    <span class="log-time">{{ $request->targetAttendance->created_at->format('Y-m-d H:i') }}</span>
                    <span style="color: #666;">{{ $request->targetAttendance->type->label() }}</span>
                    <span style="color: #666;">→</span>
                    <span style="font-weight: bold;">{{ $request->new_time->format('Y-m-d H:i') }}</span>
                    @if ($request->status === 'pending')
                        <span style="background: #fef3c7; color: #92400e; padding: 2px 10px; border-radius: 12px; font-size: 12px;">承認待ち</span>
                    @elseif ($request->status === 'approved')
                        <span style="background: #d1fae5; color: #047857; padding: 2px 10px; border-radius: 12px; font-size: 12px;">承認済</span>
                    @elseif ($request->status === 'rejected')
                        <span style="background: #fee2e2; color: #991b1b; padding: 2px 10px; border-radius: 12px; font-size: 12px;">却下</span>
                    @endif
                </div>
                <div style="color: #555; font-size: 13px; margin-top: 4px;">
                    理由: {{ $request->reason }}
                </div>

                
                <div style="color: #888; font-size: 11px; margin-top: 4px;">
                    申請日時: {{ $request->created_at->format('Y-m-d H:i') }}
                </div>
                @can('update', $request)
                    <div style="margin-top: 10px; display: flex; gap: 8px;">
                        <a href="{{ route('attendance-requests.edit', $request) }}"
                           style="padding: 4px 12px; background: #06c; color: white; text-decoration: none; border-radius: 4px; font-size: 12px;">
                            修正
                        </a>
                        <form action="{{ route('attendance-requests.destroy', $request) }}" method="POST"
                              onsubmit="return confirm('この申請を取り下げますか?');" style="display: inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    style="padding: 4px 12px; background: #e74c3c; color: white; border: none; border-radius: 4px; font-size: 12px; cursor: pointer;">
                                取り下げ
                            </button>
                        </form>
                    </div>
                @endcan
            </div>
        </div>
    @empty
        <p style="margin-top: 20px; color: #888; text-align: center; padding: 40px 0;">
            まだ申請はありません。
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