<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ChangeLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $get_lang = $request->header('lang');
        $locale = session('locale') ?: config('app.locale');
        if ($get_lang) {
            $lang = $get_lang;
            $locales = config('app.locales');
            if (in_array($lang, $locales)) {
                session(['locale' => $lang]);
                $locale = $lang;
            }
        }
        app()->setLocale($locale);
        return $next($request);
    }
}
