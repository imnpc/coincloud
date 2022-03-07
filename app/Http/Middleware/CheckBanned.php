<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Laravel\Passport\Token;

class CheckBanned
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if (auth()->check() && (auth()->user()->is_banned == 1)) {
            // 移除所有已登录 token TODO
//            Token::where('user_id', auth()->user()->id)
//                ->delete();
            $data['message'] = '您的账户已被暂停, 请联系网站管理员.';
            return response()->json($data, 403);
        } else {
            return $next($request);
        }
    }
}
