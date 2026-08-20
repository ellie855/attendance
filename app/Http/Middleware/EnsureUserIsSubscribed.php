<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsSubscribed
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 未ログインは弾く (authの後で使う想定だが念のため)
        if (! $request->user()) {
            abort(401);
        }

        // 管理者はProチェックをバイパス（全機能利用許可）
        if ($request->user()->role === 'admin') {
            return $next($request);
        }

        // Pro加入者じゃなければ料金プランへ誘導
        if (! $request->user()->subscribed('default')) {
            return redirect()
                ->route('billing.index')
                ->with('error', 'この機能はProプラン限定です');
        }

        // OK、次へ通す
        return $next($request);
    }
}
