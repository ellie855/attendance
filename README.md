# StepLog(勤怠管理 SaaS)

Laravel + PHP で作成した勤怠管理アプリのポートフォリオです。
打刻・修正申請・休暇申請・月次レポート・CSV 入出力までひと通り実装しています。

**🌐 デモ**: http://attendance.163.44.100.230.nip.io/

---

## 主な機能

### 一般ユーザー
- 出勤・退勤・休憩開始・休憩終了の打刻(日付跨ぎ勤務対応)
- 今月の勤務時間サマリー(休憩時間差引済み)
- 月次レポート表示 + **CSV エクスポート**
- 打刻修正申請(既存打刻の修正 + 打刻の追加申請)
- 休暇申請(有休/欠勤/半休/特別休暇)
- 今日の作業内容メモ
- 通知一覧(ベルアイコン + 未読バッジ)

### 管理者
- ユーザー管理(役割変更、削除)
- **CSV による ユーザー一括登録**(バルクインサート最適化)
- 修正申請の承認・却下
- 休暇申請の承認・却下

---

## 技術スタック

| カテゴリ | 使用技術 |
|---|---|
| バックエンド | Laravel 13, PHP 8.4 |
| フロントエンド | Blade, Vite, Bootstrap Icons |
| DB | MySQL 8(Docker) |
| テスト | Pest |
| 本番環境 | さくら VPS + Nginx + PHP-FPM |

---

## アピールポイント

### N+1 問題解消 + バルクインサート
CSV による1000ユーザー登録処理を **280秒 → 1.6秒(約170倍)** に改善。
- 1行ずつの INSERT/SELECT → 1回の SELECT + バルクINSERT
- `DB::transaction()` + `array_chunk()`

### 型安全な Enum
勤怠種別・休暇種別・修正申請種別を Backed Enum で表現。
- `AttendanceType`, `LeaveType`, `CorrectionRequestType`
- ラベル取得メソッド付き、Model の `$casts` で自動キャスト

### 休憩時間の差引ロジック
勤務時間計算に休憩時間を自動反映(日次/週次/月次/経過時間)。
JS 側も勤務中/休憩中の状態で経過時間を切り替え表示。

### 権限管理
`Policy` を利用して申請の編集・取り下げは本人のみ許可、
管理者機能は独自 middleware で保護。

### テスト(Pest)
Feature テストで主要動線をカバー:
- 認証、打刻、CSV アップロード、権限境界(403)

---

## セットアップ(ローカル)

```bash
git clone https://github.com/ellie855/ayutonLAB.git
cd ayutonLAB

composer install
npm install

cp .env.example .env
php artisan key:generate

# DB(Docker)
docker start php-study-db

# マイグレーション
php artisan migrate

# 起動
php artisan serve   # http://127.0.0.1:8000
npm run dev
```

---

## テスト実行

```bash
php vendor/bin/pest
```

---

## ディレクトリ構成(主要部分)

```
app/
├── Enums/                 # AttendanceType / LeaveType / CorrectionRequestType
├── Http/Controllers/
│   ├── Admin/             # 管理者機能
│   └── AttendanceController.php など
├── Models/                # Eloquent モデル
├── Notifications/         # アプリ内通知
└── Policies/              # 権限ロジック

resources/views/
├── layouts/app.blade.php  # 共通レイアウト
└── attendances/ ...       # 各画面
```

---

## 学習メモ

実装過程での学びは [`notes/laravel-learning.md`](notes/laravel-learning.md) に随時記録。
