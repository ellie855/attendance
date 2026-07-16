
@extends('layouts.app')

@section('title', '投稿編集')

@section('content')
    <h1>投稿編集</h1>

    <p><a href="{{ route('posts.index') }}">← 一覧に戻る</a></p>

    <form action="{{ route('posts.update', $post) }}" method="POST">
        @csrf
        @method('PUT')

        <label for="title">タイトル</label>
        <input type="text" id="title" name="title" value="{{ old('title', $post->title) }}">
        @error('title') <div class="error">{{ $message }}</div> @enderror

        <label for="body">本文</label>
        <textarea id="body" name="body">{{ old('body', $post->body) }}</textarea>
        @error('body') <div class="error">{{ $message }}</div> @enderror

        <p><button type="submit">更新する</button></p>
    </form>
@endsection