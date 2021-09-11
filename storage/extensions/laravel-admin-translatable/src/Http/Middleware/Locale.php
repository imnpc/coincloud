<?php

namespace solutionforest\LaravelAdmin\Translatable\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class Locale
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
      
        $locale = session('locale') ?: config('app.locale');
        // dd($request->all());
        // dd(app(),$locale,$request,request(),$_GET,$request->route());
        if (request()->has('lang')) {
            $lang = request()->input('lang');
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
