<?php 
namespace App\Http\Middleware;

use Closure;

class Cors {

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next) {
        if($request->has('export')){
            return $next($request);
        }else{
            $allowedOrigins = [url('/')];
            $origin = isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : '';
            //if (in_array($origin, $allowedOrigins)) {
                return $next($request)
                    ->header('Access-Control-Allow-Origin', '*')
                    ->header('Access-Control-Allow-Methods', 'POST, GET, PUT, DELETE, OPTIONS')
                    ->header('Access-Control-Allow-Headers',' Accept,Authorization, Content-Type');
                    //->header('Access-Control-Allow-Credentials',' true');
            //}
            return $next($request);
        }
    }
}