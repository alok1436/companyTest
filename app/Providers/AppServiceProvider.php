<?php

namespace App\Providers;
use View;
use Auth;
use App\Notification;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
         view()->composer('*', function ($view){
            if(Auth::check()){
                $role = session()->get('role');
                view::share('numberAlert',Notification::numberAlert());
                view::share('role',$role);
            }

            if(request()->filled('np_hash')){
                Notification::where(['id'=>request()->np_hash,'user_id'=>Auth::id()])->update(['read_at'=>date('Y-m-d H:i:s')]);
            }

            if(request()->filled('read') && request()->read == 1){ 
                Notification::where('user_id',Auth::id())->update(['read_at'=>date('Y-m-d H:i:s')]);
                return redirect('admin/notifications');
            }
        });
    }
}
