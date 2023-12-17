<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminCheck
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
            if(auth()->user()->role == 'admin'){
                return $next($request);
            }else{
                return redirect()->route('dashboard')->with('error','Maaf anda bukan admin');
            }
        
        //    jika tidak memiliki akses maka kembalikan ke halaman login
    }
}
