@extends('layouts.app')

@section('title', 'サブスク管理')

@section('content')
<div class="max-w-2xl mx-auto py-10 px-4">
    <h1 class="text-2xl font-bold mb-6">サブスクリプション管理</h1>

    {{-- フラッシュメッセージ --}}
    @if (session('success'))
        <div style="background: #d4edda; color: #155724; padding: 12px; margin-bottom: 16px; border-radius: 6px;">
            ✓{{ session('success') }}
        </div>
    @endif
    @if (session('error'))
        <div style="background: #f8d7da; color: #721c24; padding: 12px; margin-bottom: 16px; border-radius: 6px;">
            ⚠{{ session('error') }}
        </div>
    @endif

    <div class="border rounded-lg p-6 bg-white shadow">
        <div class="mb-4">
            <span class="text-sm text-gray-600">現在のプラン</span>
            <div class="text-xl font-bold text-blue-600">Pro (¥990 / 月)</div>
        </div>

        @if ($subscription->onGracePeriod())
            {{-- 解約予約中 --}}
            <div style="background: #fef3c7; color: #92400e; padding: 12px; border-radius: 6px; margin-bottom: 16px;">
                ⚠解約予約中
                <div class="text-sm mt-1">
                    利用可能期限: <strong>{{ $subscription->ends_at->format('Y年n月j日') }}</strong>まで
                </div>
            </div>

            {{-- 解約取り消しボタン（将来実装）--}}
            <p class="text-sm text-gray-600">
                期限まではPro機能を引き続きご利用いただけます。 
            </p>
            
            {{-- Stripe公式ポータル --}}
            <div style="margin-top: 24px; padding-top: 20px; border-top: 1px solid #e5e7eb;">
                <div class="text-sm text-gray-600 mb-2">より詳細な設定はこちら</div>
                <a href="{{ route('billing.portal') }}"
                    style="padding: 8px 16px; background: #6b7280; color: white; text-decoration: none; border-radius: 6px; font-size: 14px; display: inline-block;">
                    <i class="bi bi-gear"></i> Stripe管理ポータルを開く
                </a>
                <div class="text-xs text-gray-500 mt-2">
                    カード情報の変更、請求書のダウンロード、解約の取り消し、などが可能です。
                </div>
            </div>

        @else
            {{-- アクティブ --}}
            <div style="background: #d1fae5; color: #065f46; padding: 12px; border-radius: 6px; margin-bottom: 16px;">
                ✅ ご利用中
            </div>

            <form method="POST" action="{{ route('billing.cancel-subscription') }}"
                onsubmit="return confirm('本当に解約しますか？次回請求日まではProプランをご利用いただけます');">
                @csrf
                <button type="submit"
                    style="padding: 8px 16px; background: #dc2626; color: white; border: none; border-radius: 6px; cursor: pointer;">
                    解約する
                </button>
            </form>

            {{-- Stripe公式ポータル --}}
            <div style="margin-top: 24px; padding-top: 20px; border-top: 1px solid #e5e7eb;">
                <div class="text-sm text-gray-600 mb-2">より詳細な設定はこちら</div>
                <a href="{{ route('billing.portal') }}"
                    style="padding: 8px 16px; background: #6b7280; color: white; text-decoration: none; border-radius: 6px; font-size: 14px; display: inline-block;">
                    <i class="bi bi-gear"></i> Stripe管理ポータルを開く
                </a>
                <div class="text-xs text-gray-500 mt-2">
                    カード情報の変更、請求書のダウンロード、解約の取り消し、などが可能です。
                </div>
            </div>
        @endif
    </div>    
</div>
@endsection