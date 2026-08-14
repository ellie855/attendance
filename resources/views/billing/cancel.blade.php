@extends('layouts.app')

@section('title', '決済キャンセル')

@section('content')
<div class="max-w-xl mx-auto py-16 px-4 text-center">
    <div class="text-6xl mb-4">😢</div>
    <h1 class="text-2xl font-bold mb-4">決済がキャンセルされました</h1>
    <p class="text-gray-600 mb-8">
        いつでもProプランにアップグレードできます。
    </p>
    <a href="{{ route('billing.index') }}"
        class="inline-block bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">
        料金プランに戻る
    </a>
</div>
@endsection