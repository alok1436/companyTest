<?php
namespace App\Http\Controllers\API;
  
use Session;
use Validator;
use App\Role;
use App\User;
use Socialite;
use Exception;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Http\Request; 
use App\Mail\ResendOtp;
use App\Mail\RetrivePassword;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth; 
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use App\Services\SocialFacebookAccountService;
use App\Mail\Customer\CustomerRegistered;

class AuthenticationController extends Controller
{

    /** 
     * social login api 
     * 
     * @return \Illuminate\Http\Response 
     */ 
    
    public function social_login(Request $request){

        if( $request->isMethod('post')){
              
             $messages = [
              'type.required' => 'Type is required.',
              'first_name.required' => 'First Name field is required.',
              'last_name.required' => 'Last Name field is required.',
              'provider_id.required' => 'Provider id is required.',
            ];
            $validator = Validator::make($request->all(), [
                'first_name' => request()->type !='apple' ? 'required' : '',
                'last_name'  => request()->type !='apple' ? 'required' : '',
                'type' => 'required',
                'provider_id' => 'required',
            ],$messages);

                if ($validator->fails()) {
                    return response()->json(['error'=>$validator->errors()], 401);            
                } 

                if($request->filled('email')){
                    $user = User::whereEmail($request->get('email'))->first();
                }else{
                    $user = User::where(request()->type.'_id',request()->provider_id)->first();    
                }
                
                if($user){
                        Auth::login($user);
                        $tokenResult = $user->createToken($user->email.rand(11111,99999));
                        $token = $tokenResult->token;
                        if ($request->remember_me)
                                $token->expires_at = Carbon::now()->addWeeks(1);
                        $token->save();
                        // Authentication passed....
                        $type = request()->type.'_id';
                        $user->$type = request()->provider_id;

                        $user->save();
                        return response()->json([
                            'success' => 200,
                            'message' =>'Login successfull.',
                            'results' => [
                                'token'   => $tokenResult->accessToken,
                                'token_type' => 'Bearer',
                                'expires_at' => Carbon::parse(
                                        $tokenResult->token->expires_at
                                )->toDateTimeString(),
                                'role'    => Auth::user()->roles()->get()->first()->name,
                                'data'    => [
                                            'id'=>Auth::user()->id,
                                            'first_name'=>Auth::user()->first_name,
                                            'last_name'=>Auth::user()->last_name,
                                            'email'=>Auth::user()->email,
                                            'phone'=>Auth::user()->phone
                                        ]
                            ]
                        ]);
                }else{
               
                $user = User::create([
                        'first_name'=> request()->first_name,
                        'last_name' => request()->last_name, 
                        'email' => request()->email ? request()->email : '', 
                        request()->type.'_id' => request()->provider_id, 
                        'is_email_verified'=>'Yes', 
                        'status'=>'Active']
                    );

                if($user){
                    $user->roles()->attach(Role::where('name', 'customer')->first());
                    Auth::login($user);
                    $tokenResult = $user->createToken($user->provider_id.rand(11111,99999));
                    $token = $tokenResult->token;
                    if ($request->remember_me)
                            $token->expires_at = Carbon::now()->addWeeks(1);
                    $token->save();
                    
                    // Authentication passed...

                    return response()->json([
                        'success' => 200,
                        'message' =>'Login successfull.',
                        'results' => [
                            'token'   => $tokenResult->accessToken,
                            'token_type' => 'Bearer',
                            'expires_at' => Carbon::parse(
                                    $tokenResult->token->expires_at
                            )->toDateTimeString(),
                            'role'  => Auth::user()->roles()->get()->first()->name,
                            'data'  => [
                                        'id'=>Auth::user()->id,
                                        'first_name'=>Auth::user()->first_name,
                                        'last_name'=>Auth::user()->last_name,
                                        'email'=>Auth::user()->email,
                                        'phone'=>Auth::user()->phone
                                    ]
                        ]
                    ]);
                }else{
                    return response()->json([
                        'success' => 401,
                        'message' =>'Someting went wrong, please check and try again.'
                    ],401);
                }
            }
        }       
    }

    
    

}