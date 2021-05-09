<?php

namespace App\Http\Middleware;

use Closure;

class ChangeResponseStructureMiddleware
{
    /**
     * 最后处理返回结果 转成需要的格式
     * @param $request
     * @param Closure $next
     * @return \Illuminate\Http\JsonResponse
     */
    public function handle($request, Closure $next)
    {
        $response = $next($request);
        if ($request->is('api/v1/*') || $request->is('oauth/*') ||!$request->is('api/email/verify/*')) {
            $original = $response->getContent();
            $code = $response->getStatusCode();
            if ($code != 200 || $code != 201) {
                $code = 200; // 强制返回状态码非 200 201 的为 200
            }
            $newContent = [
                'code' => $response->getStatusCode(),
                'data' => json_decode($original),
            ];

            return response()->json($newContent, $code);// 返回结果和状态码
        } else {
            return $next($request);
        }
    }
}
