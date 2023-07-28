<?php

namespace App\Http\Middleware;

use Closure;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $role)
    {
        if(auth()->check() && auth()->user()->role === $role){
            return $next($request);
        }
        if(auth()->user()->role == 'administrator'){
            return redirect()->route('administrator.home');
        }
        if(auth()->user()->role == 'manager'){
            return redirect()->route('manager.home');
        }
        if(auth()->user()->role == 'reviewer'){
            return redirect()->route('reviewer.home');
        }
        if(auth()->user()->role == 'capturist'){
            return redirect()->route('capturist.home');
        }
    }
}
