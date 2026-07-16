@extends('layouts.app')

@section('title', $post->title)

@section('content')
    <p><a href="{{ route('posts.index') }}">← 一覧に戻る</a></p>

    <h1>{{ $post->title }}</h1>
    <div class="meta">{{ $post->created_at->format('Y-m-d H:i') }}</div>
    <div class="body">{{ $post->body }}</div>

    <p style="margin-top: 24px;">
        <a href="{{ route('posts.edit', $post) }}">編集</a>
        <form class="inline" action="{{ route('posts.destroy', $post) }}" method="POST"
              onsubmit="return confirm('削除しますか?');">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn-danger">削除</button>
        </form>
    </p>
@endsection
