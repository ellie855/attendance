# Laravel やったことメモ

ルール: 1日1〜3行でOK、書きたくない日は書かない。

---

## 6月

### 6/19
- 休憩ボタン作った(出勤・退勤・休憩開始・休憩終了の4種類)
- 履歴が全部「退勤」って表示されるバグはまった
  → 原因: enum って機能使ったら、文字列じゃなくなったのに古い書き方のままだった
  → 直し方: `{{ $attendance->type->value }}` みたいに `->value` を後ろにつける

### 6/22
- 「今日の作業内容」を書ける機能つくり始めた
- migration の書き方間違えてエラー出た
  → 正解は `$table->foreignId('user_id')` の形(型 + カラム名)
- PC 再起動したら DB 動かなくなった
  → `docker start php-study-db` で起動。`docker ps` で動いてるか確認できる
- 過去の DB データ消えた、悲しい(別のコンテナにあったみたい)

### 7/2
- 「今日の作業内容」保存機能できた + プロっぽいUX追加
  - フラッシュメッセージ:「保存しました」が1.5秒で自動で消える
  - 保存状態バッジ:「保存済み」「未保存の変更あり」を常時表示
- 学び: setTimeout でフェード → 消す、input イベントで文字入力を監視
- Pint という自動整形ツール発見 → 学習中は使わないほうがいいらしい
- CSS はプロジェクトで書き場所を統一するべき(app.blade.php に集約)

### 7/2(続き)
- 「勤怠修正申請」ボタンから遷移する空の画面作った(Step 1 だけ)
- Controller は `class { function { ... } }` の入れ子構造が必要
  → class の直下に return は書けない、function で囲む
- use 文の名前を1文字間違えると「クラス見つからない」エラー
  → 例: `AttendanceRequestControlle`(r 抜け)でハマった

---

## カッコの意味(これ知っとくと急に読めるようになる)

### `()` 丸カッコ = 動作を実行 + データを渡す
```
auth()              動作を呼ぶ
route('foo')       routeに 'foo' を渡す
today()             今の日付を取ってくる動作
```
中身に何か入ってたら「これを渡してね」って意味。

### `[]` 角カッコ = リスト(配列)
```
['りんご', 'みかん']    複数のもの並べた箱
['user_id', 'date']    名前2つ並べたもの
```

### `{}` 波カッコ = ここからここまでが1かたまり
```
if (..) {
    ここに条件が真の時にやること
}
```
「中身は1セットだよ」と囲む。

### `''` `""` = 文字列(ただの文字)
```
'clock_in'    「clock_in」という文字
"hello"       「hello」という文字
```
普段は `''` でOK。

### `{{ }}` = Blade で値を表示
```
{{ $user->name }}    画面にユーザー名を出す
```
View ファイル限定。

---

## よく出る単語(自分の言葉メモ)

| 単語 | ざっくり |
|---|---|
| Controller | URL叩かれた時に何するか書く場所(AttendanceController.php とか) |
| View | 画面を作る場所(.blade.php のファイル) |
| Model | DBの1テーブルに対応するPHPクラス(User.php とか) |
| migration | DBの設計図ファイル。`php artisan migrate` で反映 |
| Route | URLとControllerをつなぐ表(routes/web.php) |
| enum | 決まった選択肢だけ許す型。タイポ防止 |
| null | 空っぽ(0や"" と違う「何もない」状態) |
| 配列 | リスト `['a', 'b']` |
| 連想配列 | 名前付きリスト `['name' => '田中']` |
| メソッド | 動作。`()` 付き(`save()` とか) |
| プロパティ | 値。`->` で取る(`$user->name`) |
| GET | 「ページください」(見るだけ) |
| POST | 「これ記録して」(書き込みあり) |
| CSRF | フォーム偽装攻撃対策。`@csrf` 書けばOK、お守り |
| タイポ | 打ち間違い(typo)。1文字違うだけでバグる、自分もAIも普通にやる |
| インデント | 行頭の空白の揃え具合。読みやすさのため、Laravelはスペース4つ |
| camelCase | ラクダ。最初小文字、途中の単語頭を大文字(例: `breakStart`)。メソッド・変数 |
| PascalCase | 全単語の頭を大文字(例: `BreakStart`)。クラス名・enum case |
| snake_case | 全部小文字 + `_`(例: `break_start`)。DBのカラム・テーブル名 |
| kebab-case | 全部小文字 + `-`(例: `break-start`)。URL・CSSクラス |

---

## よく使うコマンド
```
php artisan serve              サーバー起動(閉じない)
php artisan migrate            DBにテーブル反映
php artisan make:model 名前    モデル作成
docker ps                      動いてるコンテナ確認
docker start php-study-db      DB起動
```

## ターミナルは2つ開く
- 1つは `php artisan serve` 動かしっぱなし
- もう1つで migrate とか他のコマンド

## 詰まった時の確認順
1. `php artisan serve` 動いてる?
2. `docker ps` で MySQL コンテナ動いてる?
3. エラー画面の一番上の赤い文字を読む(英語でも雰囲気で分かる)
