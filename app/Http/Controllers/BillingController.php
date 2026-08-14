<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BillingController extends Controller
{
    // 料金プランページ表示
    public function index()
    {
        return view('billing.index');
    }

    // Stripe Checkout Session 作成 → Stripe決済画面へリダイレクト
    public function checkout(Request $request)
    {
        $priceId = 'price_1U1mJHIfChzn5DCSmtPya9uT'; // Pro Plan の Price ID

        return $request->user()
            ->newSubscription('default', $priceId)
            ->checkout([
                'success_url' => route('billing.success'),
                'cancel_url'  => route('billing.cancel'),
            ]);
    }

    // 決済成功後の画面
    public function success()
    {
        return view('billing.success');
    }

    // 決済キャンセル後の画面
    public function cancel()
    {
        return view('billing.cancel');
    }
}
