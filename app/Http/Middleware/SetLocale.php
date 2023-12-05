<?php

namespace App\Http\Middleware;

use Closure;

class SetLocale
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
		// app()->setLocale($request->segment(1));
        if (session('lang')) {
            app()->setLocale(session('lang'));
        } else {
            session(['lang' => 'hy']);
            app()->setLocale('hy');
        }

		return $next($request);
	}
}