<?php

namespace App\Http\Controllers\Auth;

use Auth;
use Session;
use App\Role;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
       $this->middleware('guest')->except('logout');
    }

    public function logOut() {
        Auth::logout();
        return redirect('login');
    }

    public function redirectTo()
    {    
        switch(Auth::user()->roles()->get()->first()->name){
            case 'admin':
				$this->redirectTo = RouteServiceProvider::HOME;
				break;
			case 'class':
				$this->redirectTo = RouteServiceProvider::TEACHER_HOME;
				break;
			case 'student':
				$this->redirectTo = RouteServiceProvider::STUDENT_HOME;
				break;
        }
		return $this->redirectTo;
    }
}
