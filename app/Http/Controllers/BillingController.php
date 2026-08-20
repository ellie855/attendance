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

    // サブスク管理ページ表示(Pro加入者のみ)
    public function manage(Request $request)
    {
        $user = $request->user();

        // 未加入なら料金プランへ
        if (! $user->subscribed('default')) {
            return redirect()->route('billing.index')
                ->with('error', 'Proプランに加入していません');
        }

        $subscription = $user->subscription('default');

        return view('billing.manage', compact('subscription'));
    }

    // 解約実行(次回請求日で終了)
    public function cancelSubscription(Request $request)
    {
        $user = $request->user();

        if (! $user->subscribed('default')) {
            return back()->with('error', 'Proプランに加入していません');
        }

        // Cashier: cancel() は「期間終了で終わる」予約(即停止ではない)
        $user->subscription('default')->cancel();

        return redirect()->route('billing.manage')
            ->with('success', '解約を受け付けました。次回請求日まではProプランをご利用いただけます');
    }
    // Stripe Billing Portalへリダイレクト(解約/カード変更/請求書DL 全部Stripe任せ)
    public function portal(Request $request)
    {
        return $request->user()->redirectToBillingPortal(
            route('billing.manage') // Portalから戻ってきたときの遷移先
        );
    }
}
