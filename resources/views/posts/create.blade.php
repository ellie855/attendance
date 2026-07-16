@extends('layouts.app')

@section('title', '新規投稿')

@section('content')
    <h1>新規投稿</h1>

    <p><a href="{{ route('posts.index') }}">← 一覧に戻る</a></p>

    <form action="{{ route('posts.store') }}" method="POST">
        @csrf

        <label for="title">タイトル</label>
        <input type="text" id="title" name="title" value="{{ old('title') }}">
        @error('title') <div class="error">{{ $message }}</div> @enderror

        <label for="body">本文</label>
        <textarea id="body" name="body">{{ old('body') }}</textarea>
        @error('body') <div class="error">{{ $message }}</div> @enderror

        <p><button type="submit">投稿する</button></p>
    </form>
@endsection