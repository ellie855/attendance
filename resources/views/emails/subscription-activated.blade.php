<x-mail::message>
# Proプランへのご登録ありがとうございます🎉

{{ $name }}様

このたびは **StepLog Proプラン**にご登録いただき、誠にありがとうございます。

Proプランでは以下の機能をご利用いただけます:

- ✅ ユーザー数無制限
- ✅ 月次レポートの CSV ダウンロード
- ✅ 大量データのバルク処理

<x-mail::button :url="url('/attendances')">
勤怠画面を開く
</x-mail::button>

ご不明な点があればお気軽にお問い合わせください。
今後ともStepLogをよろしくお願いいたします。

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>