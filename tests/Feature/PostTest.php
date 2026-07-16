<?php

use App\Models\Post;

test('投稿一覧ページが表示される', function () {
    $response = $this->get('/posts');

    $response->assertStatus(200);
    $response->assertSee('掲示板');
});

test('投稿を新規作成できる', function () {
    $response = $this->post('/posts', [
        'title' => 'テスト投稿',
        'body'  => 'これはテスト投稿の本文です。',
    ]);

    $response->assertRedirect('/posts');

    $this->assertDatabaseHas('posts', [
        'title' => 'テスト投稿',
        'body'  => 'これはテスト投稿の本文です。',
    ]);
});

test('タイトルなしの投稿はバリデーションエラーになる', function () {
    $response = $this->post('/posts', [
        'title' => '',
        'body'  => '本文だけある',
    ]);

    $response->assertSessionHasErrors('title');
    $this->assertDatabaseCount('posts', 0);
});