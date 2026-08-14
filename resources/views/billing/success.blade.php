@extends('layouts.app')

@section('title', '決済完了')

@section('content')
<div class="max-w-xl mx-auto py-16 px-4 text-center">
    <div class="text-6xl mb-4">🎉</div>
    <h1 class="text-2xl font-bold mb-4">Proプランへようこそ！</h1>
    <p class="text-gray-600 mb-8">
        決済が完了しました。<br>
        すべてのPro機能がご利用いただけます。
    </p>
    <a href="{{ route('attendances.index') }}"
        class="inline-block bg-blue-600 text-white px-6 py-2  rounded hover:bg-blue-700">
        勤怠画面に戻る
    </a>
</div>
@endsection