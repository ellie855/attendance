@extends('layouts.app')

@section('title', '料金プラン')

@section('content')
<div class="max-w-4xl mx-auto py-10 px-4">
    <h1 class="text-3xl font-bold text-center mb-8">料金プラン</h1>

    <div class="grid md:grid-cols-2 gap-6">

        {{-- Freeプラン --}}
        <div class="border rounded-lg p-6 bg-white shadow">
            <h2 class="text-xl font-bold mb-2">Free</h2>
            <p class="text-3xl font-bold mb-4">￥0<span class="text-sm font-normal">/月</span></p>
            <ul class="text-sm space-y-1 mb-6">
                <li>✅勤怠打刻</li>
                <li>✅月次レポート</li>
                <li>❌CSVエクスポート</li>
                <li>❌ユーザー無制限</li>
            </ul>
            <button class="w-full bg-gray-300 text-gray-700 py-2 rounded" disabled>
                現在のプラン
            </button>
        </div>

        {{-- Proプラン --}}
        <div class="border-2 border-blue-500 rounded-lg p-6 bg-white shadow">
            <h2 class="text-xl font-bold mb-2 text-blue-600">Pro</h2>
            <p class="text-3xl font-bold mb-4">￥990<span class="text-sm font-normal">/月</span></p>
            <ul class="text-sm space-y-1 mb-6">
                <li>✅勤怠打刻</li>
                <li>✅月次レポート</li>
                <li>✅CSVエクスポート</li>
                <li>✅ユーザー無制限</li>
            </ul>
            <form method="POST" action="{{ route('billing.checkout') }}">
                @csrf
                <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700">
                    Proにアップグレード
                </button>
            </form>
        </div>

    </div>
</div>
@endsection