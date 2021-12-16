<?php
namespace App\Http\Controllers\API;

use FCM; 
use Session;
use Socialite;
use Exception;
use Validator;
use App\Role;
use App\User;
use Carbon\Carbon;
use App\Notification;
use App\Mail\ResendOtp;
use Illuminate\Support\Str;
use Illuminate\Http\Request; 
use App\Mail\RetrivePassword;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth; 
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;    
use LaravelFCM\Message\OptionsBuilder;
use Illuminate\Support\Facades\Password;
use App\Mail\Customer\CustomerRegistered;
use LaravelFCM\Message\PayloadDataBuilder;
use App\Services\SocialFacebookAccountService;
use LaravelFCM\Message\PayloadNotificationBuilder;

class NotificationApiController extends Controller
{
     /**
     * get notificatons.
     * Checks phone and used to send the message
     * @Role: API
     * @param  \Illuminate\Http\Request  $request
     * @return JSON
     */
    public function get(Request $request)
    {
        $user = auth('api')->user();
        if($user){
            return response()->json([
                    'success' => 200,
                    'results' => $user->notifications()->orderBy('id','DESC')->get(),
                    'message' =>'Notifications loaded',
            ]);
        }else{
            return response()->json([
                'success' => 401,
                'message' =>'This account seems doesn\'t exists, please check and try again.'
            ],401);
        } 
    }
    /**
     * get notificatons.
     * Checks phone and used to send the message
     * @Role: API
     * @param  \Illuminate\Http\Request  $request
     * @return JSON
     */
    public function remove(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:App\Notification,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['error'=>$validator->errors()], 401);            
        }

        $user = auth('api')->user()->notifications()->where('id', $request->id)->delete();
        return response()->json([
                'success' => 200,
                'message' =>'Notifications deleted',
        ]);
    }
     /**
     * read notificatons.
     * 
     * @Role: API
     * @param  \Illuminate\Http\Request  $request
     * @return JSON
     */
    public function read(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:App\Notification,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['error'=>$validator->errors()], 401);            
        }

        $notification = auth('api')->user()->notifications()->where('id',$request->id)->first();
        if($notification){
            $notification->read_at = date('Y-m-d H:i:s');
            $notification->save(); 
            return response()->json([
                    'success' => 200,
                    'message' =>'Notification readed',
            ]);                       
        }else{
            return response()->json([
                'success' => 401,
                'message' =>'Something went wrong'
            ],401);           
        }
    }

    public function create(Request $r){
        $notification = new Notification();
        $user = auth('api')->user(); //dd($user->device_token);
        $notification->user_id = $user->id;
        $notification->order_id = 11;
        if($notification->save()){
        	$click_action = route('admin.order','ORD1003742696225');
        	$data = $notification->toSingleDevice($user->device_token, $title='title', $body='body', $icon=null, $click_action);
            $notification->data=$data['payload']['data'];
            $notification->save();
            dd(json_decode($data['response']['success']));
        }
    }

    public function send(Request $r){
        $notification = new Notification();
        $data = $notification->sendPush('dh47FQaxTwWNbJgdBU2fRa:APA91bEIE99DKvL7LbY7ukxNiBs9BpW7JCYD3ue3M9DkAD8evWtBQEooPVRj7UB0rfWmsrZfB5UxtSh2fwplyzqUhL4xmNX4td8C0unKDHuKIehAqhwzLTZoQjWyiOnBGFrJIus1xu38', $title='title', $body='Testing message');
        dd($data);
    }
}