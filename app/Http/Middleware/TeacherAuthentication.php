<?php
namespace App\Http\Middleware;
use Auth;
use Closure;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Middleware\Authenticate as Middleware;

class TeacherAuthentication extends Middleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        if (Auth::check() && Auth::user()->roles()->get()->first()->name=='teacher') {
            return $next($request);
        }else if (Auth::check() && Auth::user()->roles()->get()->first()->name=='admin') {
            return redirect()->route('admin.dashboard');
        }else if (Auth::check() && Auth::user()->roles()->get()->first()->name=='student') {
            return redirect()->route('student.dashboard');
        }else{
            abort(401,'Unauthorized access');    
        }
    }
}
