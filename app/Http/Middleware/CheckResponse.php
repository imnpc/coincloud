<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckResponse
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $check = remote_check();
        if ($check['status'] == "Active") {
            return $next($request);
        } else {
            if (rand() % 2 === 0) {
                if ($request->is('api/v1/*') || $request->is('oauth/*') || !$request->is('api/email/verify/*')) {
                    $data['message'] = $check['message'];
                    $newContent = [
                        'code' => 200,
                        'data' => $data,
                    ];
                    return response()->json($newContent, 200);// 返回结果和状态码
                } else {
                    return $check['message'];
                }
            } else {
                return $next($request);
            }
        }
    }
}
