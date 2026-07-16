
@extends('layouts.app')

@section('title', '投稿一覧')

@section('content')
    <h1>掲示板</h1>

    <p><a href="{{ route('posts.create') }}">+ 新規投稿</a></p>

    @forelse ($posts as $post)
        <div class="post">
            <h3><a href="{{ route('posts.show', $post) }}">{{ $post->title }}</a></h3>
            <div class="meta">{{ $post->created_at->format('Y-m-d H:i') }}</div>
            <div class="body">{{ $post->body }}</div>
            <a href="{{ route('posts.edit',$post) }}">編集</a>
            <form class="inline" action="{{ route('posts.destroy', $post) }}" method="POST"
                  onsubmit="return confirm('削除しますか?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn-danger">削除</button>
            </form>
        </div>
    @empty
        <p>まだ投稿がありません。</p>
    @endforelse

    {{ $posts->links() }}
@endsection
