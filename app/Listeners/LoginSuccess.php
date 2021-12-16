<?php

namespace App\Listeners;
use App\Session;
use Illuminate\Http\Request;
use Illuminate\Auth\Events\Login;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class LoginSuccess
{
    /**
     * Create the event listener.
     *
     * @param  Request  $request
     * @return void
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle(Login $login)
    {
        $loginInfo = new Session();
        $loginInfo->user_id =   $login->user->id;
        $loginInfo->ip =   $this->request->ip();
        $loginInfo->device_token =   $this->request->device_token;
        $loginInfo->save(); 
    }
}
