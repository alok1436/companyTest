<?php

namespace App\Http\Middleware;

use Closure;

class ApiAuthentication
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {   //apache_request_headers()
        $token = $request->header('authorization-token');
        if ($token != "3MPHJP0BC63435345341") {
            return response()->json(['message'=>'unauthenticated'],401);
        }
        return $next($request);
    }
}
