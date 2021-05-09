<?php

namespace App\Http\Middleware;

use Closure;

class ForceHttps
{
    /**
     * 强制 API 接口使用 SSL(HTTPS) ,使用 HTTP 访问的将返回错误
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {
            return $next($request);
        } else {
            $error_message = "SSL(HTTPS) is required";
            $error_code = 403;

            $response['code'] = $error_code;
            $response['data']['message'] = $error_message;

            return response()->json($response, $error_code);// 返回结果和状态码
        }
    }
}
