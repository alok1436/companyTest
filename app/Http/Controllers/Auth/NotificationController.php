<?php
namespace App\Http\Controllers\Auth;
  
use Session;
use Validator;
use App\Role;
use App\User;
use Socialite;
use Exception;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Http\Request; 
use App\Notification;
use App\Http\Controllers\Controller;

class NotificationController extends Controller
{
    protected $notificaiton = null;
    public function __construct(Notification $notificaiton){
        $this->notificaiton = $notificaiton;
    }
    public function index(Request $r){
        if($r->ajax()){
            $html = view('auth.notification_list')->with('notificaitons',$this->notificaiton->read())->render();
            return response()->json(['success'=>true,'html'=>$html]);
        }
    }

    public function notifications(Request $r){
/*        if($r->read == 1){
            $this->notificaiton->update(['read_at',date('Y-m-d H:i:s')]);
            return redirect('admin/notifications');
        }*/
        return view('auth.notifications')->with('notificaitons',$this->notificaiton->read());
    }
}