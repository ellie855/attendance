<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;

class PostController extends Controller
{
    // 一覧表示
    public function index()
    {
        $posts = Post::latest()->paginate(5);
        return view('posts.index', ['posts' => $posts]);
    }

    // 投稿フォーム表示
    public function create()
    {
        return view('posts.create');
    }

    // 投稿を保存
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|max:100',
            'body'  => 'required',
        ]);

        Post::create($validated);

        return redirect('/posts');
    }

    // 投稿詳細表示
    public function show(Post $post)
    {
        return view('posts.show', ['post' => $post]);
    }

    // 編集フォーム表示
    public function edit(Post $post)
    {
        return view('posts.edit', ['post' => $post]);
    }

    // 投稿を更新
    public function update(Request $request, Post $post)
    {
        $validated = $request->validate([
            'title' => 'required|max:100',
            'body'  => 'required',
        ]);
        $post->update($validated);

        return redirect('/posts');
    }

    // 投稿を削除
    public function destroy(Post $post)
    {
        $post->delete();
        return redirect('/posts');
    }
}
