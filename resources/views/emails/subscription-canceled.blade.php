<x-mail::message>
# 解約を承りました

{{ $name }} 様

StepLog Proプランの解約手続きが完了しました。

<x-mail::panel>
**利用可能期限:** {{ $endsAt->format('Y年n月j日') }}
</x-mail::panel>

上記日付までは引き続き Proプランの機能をご利用いただけます。
期限を過ぎると自動的に Freeプランに切り替わります。

解約を取り消したい場合は、サブスクリプション管理画面からいつでもお手続きいただけます。

<x-mail::button :url="url('/billing/manage')">
サブスク管理画面を開く
</x-mail::button>

これまでのご利用、誠にありがとうございました。
またのご利用を心よりお待ちしております。

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
