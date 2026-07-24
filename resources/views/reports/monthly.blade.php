@extends('layouts.app')

@section('title', '月次レポート')

@section('content')

<div class="card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h1 class="card-title" style="margin: 0; border: none; padding: 0;">月次レポート</h1>
        <div style="display: flex; align-items: center; gap: 12px;">
            <a href="{{ route('reports.monthly', ['ym' => $prevMonth]) }}"
               style="padding: 6px 12px; background: #06c; color: white; text-decoration: none; border-radius: 4px; font-size: 13px;">◀ 前月</a>
            <span style="font-size: 18px; font-weight: bold;">{{ $targetMonth->format('Y年n月') }}</span>
            <a href="{{ route('reports.monthly', ['ym' => $nextMonth]) }}"
               style="padding: 6px 12px; background: #06c; color: white; text-decoration: none; border-radius: 4px; font-size: 13px;">翌月 ▶</a>
        </div>
    </div>

    <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 12px; margin-bottom: 24px;">
        <div style="background: white; padding: 16px; border-left: 4px solid #10b981; border-radius: 8px;">
            <div style="font-size: 11px; color: #666; margin-bottom: 4px;">総勤務時間</div>
            <div style="font-size: 28px; font-weight: bold;">{{ $summary['total_hours'] }}<span style="font-size: 14px; color: #888;">時間</span></div>
        </div>
        <div style="background: white; padding: 16px; border-left: 4px solid #06c; border-radius: 8px;">
            <div style="font-size: 11px; color: #666; margin-bottom: 4px;">出勤日数</div>
            <div style="font-size: 28px; font-weight: bold;">{{ $summary['work_days'] }}<span style="font-size: 14px; color: #888;">日</span></div>
        </div>
        <div style="background: white; padding: 16px; border-left: 4px solid #f59e0b; border-radius: 8px;">
            <div style="font-size: 11px; color: #666; margin-bottom: 4px;">平均勤務時間</div>
            <div style="font-size: 28px; font-weight: bold;">{{ $summary['avg_hours'] }}<span style="font-size: 14px; color: #888;">時間</span></div>
        </div>
    </div>

    <table style="width: 100%; border-collapse: collapse;">
        <thead>
            <tr style="border-bottom: 2px solid #ddd; background: #f8f8f8;">
                <th style="padding: 8px; text-align: left; font-size: 13px;">日付</th>
                <th style="padding: 8px; text-align: left; font-size: 13px;">出勤</th>
                <th style="padding: 8px; text-align: left; font-size: 13px;">退勤</th>
                <th style="padding: 8px; text-align: left; font-size: 13px;">休憩</th>
                <th style="padding: 8px; text-align: left; font-size: 13px;">勤務時間</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($dailyReport as $day)
                <tr style="border-bottom: 1px solid #eee; {{ $day['is_weekend'] ? 'background: #fef3c7;' : '' }}">
                    <td style="padding: 8px; font-family: monospace;">
                        {{ $day['date']->format('m-d') }}
                        <span style="color: #888; font-size: 11px;">({{ ['日','月','火','水','木','金','土'][$day['date']->dayOfWeek] }})</span>
                    </td>
                    <td style="padding: 8px; font-family: monospace;">
                        {{ $day['clock_in']?->format('H:i') ?? '-' }}
                    </td>
                    <td style="padding: 8px; font-family: monospace;">
                        {{ $day['clock_out']?->format('H:i') ?? '-' }}
                    </td>
                    <td style="padding: 8px; font-family: monospace; color: #666;">
                        {{ $day['break_min'] > 0 ? $day['break_min'] . '分' : '-' }}
                    </td>
                    <td style="padding: 8px; font-family: monospace; font-weight: bold;">
                        @if ($day['work_hours'] > 0 || $day['work_mins'] > 0)
                            {{ $day['work_hours'] }}時間{{ $day['work_mins'] }}分
                        @else
                            -
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

@endsection