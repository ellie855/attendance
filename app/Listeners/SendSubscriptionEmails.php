<?php

namespace App\Listeners;

use App\Mail\SubscriptionActivated;
use App\Mail\SubscriptionCanceled;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;
use Laravel\Cashier\Events\WebhookHandled;

class SendSubscriptionEmails
{
    /**
     * CasherのWebhook処理後に発火するイベントを受信し、
     * サブスク登録/解約のメールを送信する
     */
    public function handle(WebhookHandled $event): void
    {
        $payload = $event->payload;
        $type    = $payload['type'] ?? null;

        // 対象イベント以外は無視
        if (! in_array($type, [
            'customer.subscription.created',
            'customer.subscription.updated',
        ])) {
            return;
        }

        // Stripe Customer IDからユーザー特定
        $customerId = $payload['data']['object']['customer'] ?? null;
        $user = $customerId ? User::where('stripe_id', $customerId)->first() : null;
        if (! $user) {
            return;
        }

        // ①サブスク新規登録
        if ($type === 'customer.subscription.created') {
            Mail::to($user)->send(new SubscriptionActivated($user));
            return;
        }

        // ②サブスク解約(cancel_at_period_end が false → true に変わったときのみ)
        if ($type === 'customer.subscription.updated') {
            $data = $payload['data']['object'];
            $prev = $payload['data']['previous_attributes'] ?? [];

            $justCanceled = 
            ($data['cancel_at_period_end'] ?? false) === true &&
            array_key_exists('cancel_at_period_end', $prev) &&
            $prev['cancel_at_period_end'] === false;

            if($justCanceled) {
                $endsAt = Carbon::createFromTimestamp(
                    $data['cancel_at'] ?? $data['current_period_end']
                );
                Mail:to($user)->send(new SubscriptionCanceled($user, $endsAt));
            }
        }
    }
}