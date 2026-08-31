<?php

namespace App\Providers;

use App\Listeners\SendSubscriptionEmails;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Laravel\Cashier\Events\WebhookHandled;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Cashier の Webhook 処理後にサブスク通知メールを送信
        Event::listen(WebhookHandled::class, SendSubscriptionEmails::class);
    }
}
