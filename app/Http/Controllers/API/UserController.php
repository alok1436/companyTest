<?php
namespace App\Http\Controllers\API;
  
use Session;
use Validator;
use App\Role;
use App\User;
use App\DeviceToken;
use Socialite;
use Exception;
use Carbon\Carbon;
use App\Mail\ResendOtp;
use Illuminate\Support\Str;
use Illuminate\Http\Request; 
use App\Mail\RetrivePassword;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth; 
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use App\Services\SocialFacebookAccountService;
use App\Mail\Customer\ChangeMailSendOtp;
use App\Mail\Customer\CustomerRegistered;
use App\Mail\Customer\CustomerVerification;

class UserController extends Controller
{

    /** 
     * users api 
     * 
     * @return \Illuminate\Http\Response 
     */ 
    public function getUsers() 
    { 
        $users = User::get(); 
        return response()->json(['data' => $users], 200); 
    } 
    /**
     * Update the authenticated user's API token.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function update(Request $request)
    {
        $token = Str::random(60);
        $request->user()->forceFill([
            'api_token' => hash('sha256', $token),
        ])->save();
        return ['token' => $token];
    }

    /**
     * Registration new user
     * @Role: API
     * @param  \Illuminate\Http\Request  $request
     * @return JSON
     */
    public function register(Request $request){
        if( $request->isMethod('post')){

            $validator = Validator::make($request->all(), [
                'first_name' => 'required',
                'last_name' => 'required',
                'email' => 'required|email|unique:users',
                'phone' => 'required',
                'password' => 'required|min:6',
            ]);
            if ($validator->fails()) {
                return response()->json(['error'=>$validator->errors()], 401);            
            } 


            $user = new User();
            foreach($request->all() as $key=>$value){
                if( in_array( $key,$user->getFillable() ) ){
                    $user->$key = $value;
                }
            }  

            $token = Str::random(60);
            $user->api_token = hash('sha256', $token);
            $user->password = Hash::make ( $request->get ('password') );    
            $user->save();
            $user->roles()->attach(Role::where('name', 'customer')->first());

            return response()->json([
                'success' => true,
                'data'    => ['token'=>$user->api_token],
                'message' =>'Account has been created.'
            ]);
        }
    }

    /**
     * Login and authenticated user's API token.
     * @Role: API
     * @param  \Illuminate\Http\Request  $request
     * @return JSON
     */
    public function login(Request $request){

        if( $request->isMethod('post')){

            $fieldType = filter_var( $request->input, FILTER_VALIDATE_EMAIL ) ? 'Email' : 'Phone';
           
            $messages = [
            'input.required' => (($fieldType == 'Email') ? 'Email' : 'Phone') .' is required.'
            ]; 

            $rule = [
                'input'     => ($fieldType == 'Email') ? 'required|email' : 'required',
                'password'  => 'required',
                'recaptcha' => $request->debug == '' ? 'required' : ''
            ];

            $validator = Validator::make($request->all(), $rule ,$messages);
 
            if ($validator->fails()) {
                return response()->json(['error'=>$validator->errors()], 401);            
            } 
            
            //verify recaptcha
            if($request->debug == ''){
                if (verify_google_recaptcha($request->recaptcha) === false) {
                    return response()->json(['error'=>['recaptcha'=>['Recaptcha verification failed']]], 401); 
                }
            }

            if ($fieldType == 'Phone') {


                $user = User::wherePhone($request->input)->first();  
                if (!empty($user)) {

                    if (!Hash::check($request->password, $user->password)) {
                        return response()->json(['error'=>['invalid_password'=>['Incorrect password']]], 401); 
                    }
                    
                    if (Auth::loginUsingId($user->id)) {

                        if(auth::user()->status == 'Inactive' || auth::user()->is_email_verified == "No"){
                             Auth::logout();
                            return response()->json(['error'=>['message'=>'It seems your account is not verified or inactive, Please contact at support@klothus.com']], 403);
                        }

                        $requestedRole = $request->role ? $request->role : 'customer';

                        $_role = Role::where('name',$request->role)->first();
                        if($_role){
                            $count = \App\RoleRequest::where(['user_id'=>auth::user()->id,'status'=>'Pending','requested_role_id'=>$_role->id])->count();
                            if($count > 0){
                                Auth::logout();
                                return response()->json(['error'=>['message'=>'It seems your account is not approved, Please contact at support@klothus.com']], 403);
                            }
                        }
                    
                        if(auth::user()->new_role_request == 1 && auth::user()->approved_status == "Unapproved" && auth::user()->requested_role == $requestedRole){
                            Auth::logout();
                            return response()->json(['error'=>['message'=>'It seems your account is not approved, Please contact at support@klothus.com']], 403);
                        }
                        
                        $user = $request->user();
                        $tokenResult = $user->createToken($user->email.rand(11111,99999));
                        $token = $tokenResult->token;
                        if ($request->remember_me)
                                $token->expires_at = Carbon::now()->addWeeks(1);
                        $token->save();
                        
                        // Authentication passed...
                        if($user->hasRole($request->role) || $user->hasRole('customer')){
                                return response()->json([
                                'success' => 200,
                                'message' =>'Login successfull.',
                                'results' => [
                                    'token'   => $tokenResult->accessToken,
                                    'token_type' => 'Bearer',
                                    'expires_at' => Carbon::parse(
                                            $tokenResult->token->expires_at
                                    )->toDateTimeString(),
                                    'role'    => $request->role ? $request->role : 'customer',
                                    'assigned_roles'=> Auth::user()->roles()->pluck('name'),
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
                            return response()->json([
                                'success' => 401,
                                'message' =>'You don\'t have enough permission to access.'
                            ],401);
                        }
                    
                    }else{
                        return response()->json([
                            'success' => 401,
                            'message' =>'Invalid credentials, please try again.'
                        ],401);
                    }                   
                }else{
                    return response()->json([
                        'success' => 404,
                        'message' =>'Account not found, please try again.'
                    ],404);    
                }
            }else{
               
                if (Auth::attempt(['email'=>$request->input,'password'=>$request->password])) {

                    if(auth::user()->status == 'Inactive' || auth::user()->is_email_verified == "No"){
                         Auth::logout();
                        return response()->json(['error'=>['message'=>'It seems your account is not verified or inactive, please check and fix your account.']], 403);
                    }
                    
                    $requestedRole = $request->role ? $request->role : 'customer';

                    $_role = Role::where('name',$request->role)->first();
                    if($_role){
                        $count = \App\RoleRequest::where(['user_id'=>auth::user()->id,'status'=>'Pending','requested_role_id'=>$_role->id])->count();
                        if($count > 0){
                            Auth::logout();
                            return response()->json(['error'=>['message'=>'It seems your account is not approved, Please contact at support@klothus.com']], 403);
                        }
                    }
                
                    if(auth::user()->new_role_request == 1 && auth::user()->approved_status == "Unapproved" && auth::user()->requested_role == $requestedRole){
                        Auth::logout();
                        return response()->json(['error'=>['message'=>'It seems your account is not approved, Please contact at support@klothus.com']], 403);
                    }
                    
                    $user = $request->user();
                    $tokenResult = $user->createToken($user->email.rand(11111,99999));
                    $token = $tokenResult->token;
                    if ($request->remember_me)
                            $token->expires_at = Carbon::now()->addWeeks(1);
                    $token->save();
                    // Authentication passed...
                    if($user->hasRole($request->role) || $user->hasRole('customer')){

                            return response()->json([
                            'success' => 200,
                            'message' =>'Login successfull.',
                            'results' => [
                                'token'   => $tokenResult->accessToken,
                                'token_type' => 'Bearer',
                                'expires_at' => Carbon::parse(
                                        $tokenResult->token->expires_at
                                )->toDateTimeString(),
                                'role'    => $request->role ? $request->role : 'customer',
                                'assigned_roles'=> Auth::user()->roles()->pluck('name'),
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
                        return response()->json([
                            'success' => 401,
                            'message' =>'You don\'t have enough permission to access.'
                        ],401);
                    }
                }else{
                    return response()->json([
                        'success' => 401,
                        'message' =>'Invalid credentials, please try again.'
                    ],401);
                }
            }
        }
    }

    /**
     * OTP authentication.
     * Checks phone and used to loin in the application
     * @Role: API
     * @param  \Illuminate\Http\Request  $request
     * @return JSON
     */
    public function logInVerifyOtp(Request $request)
    {
        if( $request->isMethod('post')){
            $validator = Validator::make($request->all(), [
                'otp'=> 'required',
                'user_id'=> 'required'
            ]);
            if ($validator->fails()) {
                return response()->json(['error'=>$validator->errors()], 401);            
            } 

            $user = User::find($request->user_id);
            if (!$user) {
                return response()->json([
                        'success' => 404,
                        'message' =>'Account not found.'
                    ],404);
            }

            if ($user->otp != $request->otp) {
                  return response()->json([
                        'success' => 401,
                        'message' =>'Invalid otp, please try again.'
                    ],401);
            }
            if (Auth::loginUsingId($user->id)) {

                if(auth::user()->status == 'Inactive' || auth::user()->is_email_verified == "No"){
                     Auth::logout();
                    return response()->json(['error'=>['message'=>'It seems your account is not verified or inactive, please check and fix your account.']], 403);
                }
                
                $tokenResult = $user->createToken($user->email.rand(11111,99999));
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
                return response()->json([
                    'success' => 401,
                    'message' =>'Invalid credentials, please try again.'
                ],401);
            }               
        }       
    }
    /**
     * Forget password.
     * Checks phone and used to send the message
     * @Role: API
     * @param  \Illuminate\Http\Request  $request
     * @return JSON
     */
    public function send_message_to_update_password(Request $request)
    {
        //input filter check the email or phone   
        $fieldType = filter_var( $request->input, FILTER_VALIDATE_EMAIL ) ? 'Email' : 'Phone';

        $messages = [
            'input.required' => 'Email or phone required.',
            'recaptcha'=>$request->debug == '' ? 'required' : ''
        ]; 

        $validator = Validator::make($request->all(), [
            'input' => ($fieldType == 'Email') ? 'required|email' : 'required',
        ],$messages);

        if ($validator->fails()) {
            return response()->json(['error'=>$validator->errors()], 401);            
        } 

        //verify recaptcha
        if($request->debug == ''){
            if (verify_google_recaptcha($request->recaptcha) === false) {
                return response()->json(['error'=>['recaptcha'=>['Recaptcha verification failed']]], 401); 
            }
        }

        $where = "where".$fieldType;
        $user = User::$where($request->input)->first();
        
        if($user){

            $otp = mt_rand(100000, 999999);
            $user->otp = $otp;
            $user->save();
            $message ='Your OTP is '.$otp;

            if ($fieldType == 'Email') {
                try{
                    Mail::to($user->email)->send(new RetrivePassword($otp));
                    if (!Mail::failures()) {
                        return response()->json([
                            'success' => 200,
                            'message' =>'An OTP has been send on your mail, please check and confirm, thank you.',
                            'results' => [
                                'user_id' => $user->id,
                                
                            ]
                        ]);
                    }else{
                        return response()->json([
                            'success' => 401,
                            'message' =>'Something went wrong while sending the mail, please check and try again later.'
                        ],401);    
                    }
                }catch(\Swift_TransportException $e){
                    return response()->json([
                        'success' => 401,
                        'message' =>$e->getMessage()
                    ],401); 
                }
            }else{
                $response  = send_message($user->phone,$message);
                if($response['response_code'] != 1002){
                    return response()->json([
                        'success' => 200,
                        'message' =>'An OTP has been send on your phone, please check and confrim, thank you.',
                        'results' => [
                                    'user_id' => $user->id
                                ]
                    ]);
                }else{
                    return response()->json([
                        'success' => false,
                        'message' =>$response['response']
                    ],401);               
                }
            }
        }else{
            return response()->json([
                'success' => 401,
                'message' =>'This account seems doesn\'t exists, please check and try again.'
            ],401);
        } 
    }

   /**
     * Update password.
     * Used to set the new password 
     * @Role: API
     * @param  \Illuminate\Http\Request  $request
     * @return JSON
     */
    public function updatePassword(Request $request)
    {
        if( $request->isMethod('post')){
                $validator = Validator::make($request->all(), [
                    'new_password' => 'required|min:6',
                    'confirm_password' => 'required|same:new_password',
                    'recaptcha'=>$request->debug == '' ? 'required' : ''
                ]);

                if ($validator->fails()) {
                    return response()->json(['error'=>$validator->errors()], 401);            
                } 

                //verify recaptcha
                if($request->debug == ''){
                    if (verify_google_recaptcha($request->recaptcha) === false) {
                        return response()->json(['error'=>['recaptcha'=>['Recaptcha verification failed']]], 401); 
                    }
                }

                $user = auth('api')->user();  
                if (!$user) {
                    return response()->json([
                        'success' => 401,
                        'message' =>'This account doesn\'t exists, please check and try again.'
                    ],401);
                }

                $user->password = Hash::make($request->new_password);
                $user->save();

                return response()->json([
                    'success' => 200,
                    'message' =>'Your password has been changed.'
                ]);
            }
        }
   
   /**
     * Update password.
     * Used to set the new password 
     * @Role: API
     * @param  \Illuminate\Http\Request  $request
     * @return JSON
     */
    public function change_password_using_fp(Request $request)
    {
        if( $request->isMethod('post')){
                $validator = Validator::make($request->all(), [
                    'user_id'=> 'required',
                    'otp'=> 'required',
                    'new_password' => 'required|min:6',
                    'confirm_password' => 'required|same:new_password',
                    'recaptcha'=>$request->debug == '' ? 'required' : ''
                ]);

                if ($validator->fails()) {
                    return response()->json(['error'=>$validator->errors()], 401);            
                } 

                //verify recaptcha
                if($request->debug == ''){
                    if (verify_google_recaptcha($request->recaptcha) === false) {
                        return response()->json(['error'=>['recaptcha'=>['Recaptcha verification failed']]], 401); 
                    }
                }
                
                $user = User::find($request->user_id);  
                if (!$user) {
                    return response()->json([
                        'success' => 401,
                        'message' =>'This account doesn\'t exists, please check and try again.'
                    ],401);
                }

                if($user->otp != $request->otp) {
                     return response()->json([
                         'success' => 401,
                         'message' =>'Invalid otp'
                     ],401);
                }

                $user->password = Hash::make($request->new_password);
                $user->save();

                return response()->json([
                    'success' => 200,
                    'message' =>'Your password has been changed.'
                ]);
            }
        }

    /**
     * Resend otp
     * @Role: API
     * @param  \Illuminate\Http\Request  $request
     * @return JSON
     */
    public function resend_otp(Request $request){
        if( $request->isMethod('post')){

            $messages = [
                'input.required' => 'Email or phone required.',
                'recaptcha'=>$request->debug == '' ? 'required' : ''
            ]; 

            $fieldType = filter_var( $request->input, FILTER_VALIDATE_EMAIL ) ? 'Email' : 'Phone';

            $validator = Validator::make($request->all(), [
                'input' => ($fieldType == 'Email') ? 'required|email' : 'required',
            ],$messages);

            //verify recaptcha
            if($request->debug == ''){
                if (verify_google_recaptcha($request->recaptcha) === false) {
                    return response()->json(['error'=>['recaptcha'=>['Recaptcha verification failed']]], 401); 
                }
            }

            $where = "where".$fieldType;
            $user = User::$where($request->input)->first();

            if ($user) {

                    $otp = mt_rand(100000, 999999);
                    Mail::to($user->email)->send(new ResendOtp($otp));
                    $message ='Your OTP is '.$otp;
                    send_message($user->phone,$message); 
                    $user->otp = $otp;
                    $user->save();

                    if (!Mail::failures()) {
                        return response()->json([
                            'success' => 200,
                            'message' =>'A otp has been send on your mail, thank you.',
                        ]);
                    }else{
                        return response()->json([
                            'success' => 400,
                            'message' =>'Something went wrong while sending the mail, please check and try again later.'
                        ],400);                
                    } 
            }else{
                return response()->json([
                    'success' => 400,
                    'message' =>'Invalid vendor.'
                ],400);  
            }
        }
    }

    public function redirect($provider) {
        return Socialite::driver($provider)->stateless()->redirect();
    }

    public function handleCallback($provider) {
       
        $user = Socialite::driver($provider)->stateless()->user();
        $finduser = User::where('providerId', $user->id)->first();
        if ($finduser) {
            Auth::login($finduser);
            $token = Str::random(60);
            Auth::user()->forceFill([
                'api_token' => hash('sha256', $token),
            ])->save();
            return response()->json([
                'success' => 200,
                'message' =>'Login successfull.',
                'results' => [
                    'token'   => Auth::user()->api_token,
                    'role'    => Auth::user()->roles()->get()->first()->name,
                    'data'    => [
                                    'first_name'=>Auth::user()->first_name,
                                    'last_name'=>Auth::user()->first_name,
                                    'email'=>Auth::user()->email,
                                    'phone'=>Auth::user()->phone
                            
                                ]
                ]
            ]);
        }else{
            $newUser = User::create(['first_name' => $user->name, 'email' => $user->email,'providerId'=>$user->id]);
            $newUser->roles()->attach(Role::where('name', 'customer')->first());
            Auth::login($newUser);
            $token = Str::random(60);
            Auth::user()->forceFill([
                'api_token' => hash('sha256', $token),
            ])->save();
            return response()->json([
                'success' => 200,
                'message' =>'Login successfull.',
                'results' => [
                    'token'   => Auth::user()->api_token,
                    'role'    => Auth::user()->roles()->get()->first()->name,
                    'data'    => [
                                    'first_name'=>Auth::user()->first_name,
                                    'last_name'=>Auth::user()->first_name,
                                    'email'=>Auth::user()->email,
                                    'phone'=>Auth::user()->phone
                            
                                ]
                ]
            ]);
        }
    }

    /**
     * Forget password.
     * Checks phone and used to send the message
     * @Role: API
     * @param  \Illuminate\Http\Request  $request
     * @return JSON
     */
    public function change_email_or_phone_request(Request $request)
    {
        //input filter check the email or phone   
        $fieldType = filter_var( $request->input, FILTER_VALIDATE_EMAIL ) ? 'Email' : 'Phone';

        $messages = [
            'input.required' => 'Email or phone required.',
            'input.unique' =>  'An account with this '.strtolower($fieldType).' already exists.',
            'recaptcha'=>$request->debug == '' ? 'required' : ''
        ]; 

        $validator = Validator::make($request->all(), [
            'input' => ($fieldType == 'Email') ? 'required|unique:users,email,'.auth('api')->user()->id : 'required|unique:users,phone,'.auth('api')->user()->id,
        ],$messages);

        if ($validator->fails()) {
            return response()->json(['error'=>$validator->errors()], 401);            
        } 

        //verify recaptcha
        if($request->debug == ''){
            if (verify_google_recaptcha($request->recaptcha) === false) {
                return response()->json(['error'=>['recaptcha'=>['Recaptcha verification failed']]], 401); 
            }
        }

        $user = auth('api')->user();
        
        if($user){

            $otp = mt_rand(100000, 999999);
            $user->otp = $otp;
            $user->save();
            $message ='Your OTP is '.$otp;

            if ($fieldType == 'Email') {
                try{
                    Mail::to($request->input)->send(new ChangeMailSendOtp($otp));
                    if (!Mail::failures()) {
                        return response()->json([
                            'success' => 200,
                            'message' =>'An OTP has been send on your mail, please check and confirm, thank you.',
                            'results' => [
                                'user_id' => $user->id,
                                
                            ]
                        ]);
                    }else{
                        return response()->json([
                            'success' => 401,
                            'message' =>'Something went wrong while sending the mail, please check and try again later.'
                        ],401);    
                    }
                }catch(\Swift_TransportException $e){
                    return response()->json([
                        'success' => 401,
                        'message' =>$e->getMessage()
                    ],401); 
                }
            }else{
                $response  = send_message($request->input,$message);
                if($response['response_code'] != 1002){
                    return response()->json([
                        'success' => 200,
                        'message' =>'An OTP has been send on your phone, please check and confrim, thank you.',
                        'results' => [
                                    'user_id' => $user->id
                                ]
                    ]);
                }else{
                    return response()->json([
                        'success' => false,
                        'message' =>$response['response']
                    ],401);               
                }
            }
        }else{
            return response()->json([
                'success' => 401,
                'message' =>'This account seems doesn\'t exists, please check and try again.'
            ],401);
        } 
    }

    public function update_phone_or_email(Request $request){
           //input filter check the email or phone   
        $fieldType = filter_var( $request->input, FILTER_VALIDATE_EMAIL ) ? 'email' : 'phone';

        $messages = [
            'input.required' => 'Email or phone required.',
            
        ]; 

        $validator = Validator::make($request->all(), [
            'input' => ($fieldType == 'email') ? 'required|unique:users,email,'.auth('api')->user()->id : 'required|unique:users,phone,'.auth('api')->user()->id,
            'otp'=>'required',
            'recaptcha'=>$request->debug == '' ? 'required' : ''
        ],$messages);

        if ($validator->fails()) {
            return response()->json(['error'=>$validator->errors()], 401);            
        } 

        //verify recaptcha
        if($request->debug == ''){
            if (verify_google_recaptcha($request->recaptcha) === false) {
                return response()->json(['error'=>['recaptcha'=>['Recaptcha verification failed']]], 401); 
            }
        }

        $user = auth('api')->user();  

        if($user->otp == $request->otp){

            $user->$fieldType = $request->input;
            $user->save();
            return response()->json([
                'success' => 200,
                'message' =>"Thank you, your {$fieldType} has been changed."
            ],200);

        }else{
            return response()->json([
                'success' => 400,
                'message' =>'Invalid otp.'
            ],400);
        }
    }

    /**
     * update device token
     * Checks phone and used to send the message
     * @Role: API
     * @param  \Illuminate\Http\Request  $request
     * @return JSON
     */
    public function update_device_token(Request $request)
    {
      
        $validator = Validator::make($request->all(), [
            'token' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['error'=>$validator->errors()], 401);            
        } 

        $user = auth('api')->user();
        if($user){
           $deviceToken = DeviceToken::where('token',$request->get('token'))->first();
            if (empty($deviceToken)){ 
                $deviceToken = new DeviceToken;
                $deviceToken->user_id = $user->id;
                $deviceToken->token = $request->get('token');
                $deviceToken->save();

                return response()->json([
                        'success' => 200,
                        'message' =>'Token updated',
                ]);
            }else{

                return response()->json([
                        'success' => 400,
                        'message' =>'Token already exists',
                ]);
            }
        }else{
            return response()->json([
                'success' => 401,
                'message' =>'This account seems doesn\'t exists, please check and try again.'
            ],401);
        } 
    }

    public function logout(Request $request){
        $user = auth('api')->user();
        if($user){
            $accessToken = $user->token();
            $user->activeSessions()->where('device_token',$request->device_token)->delete();
            \DB::table('oauth_refresh_tokens')
                ->where('access_token_id', $accessToken->id)
                ->update([
                    'revoked' => true
                ]);

            $accessToken->revoke();
            return response()->json(null, 204);
        }
    }

    /**
     * account verification send otp
     * Checks phone and used to send the message
     * @Role: API
     * @param  \Illuminate\Http\Request  $request
     * @return JSON
     */
    public function user_verification_while_new_registration(Request $request)
    {
        //input filter check the email or phone   
        $fieldType = filter_var( $request->input, FILTER_VALIDATE_EMAIL ) ? 'email' : 'phone';

        $messages = [
            'input.required' => 'Email or phone required.',
            'recaptcha'=>$request->debug == '' ? 'required' : ''
        ]; 

        $request->merge([strtolower($fieldType) =>$request->input]);

        $where = "where".ucfirst($fieldType);

        $dump = User::$where($request->input)->first();
         
        $validator = Validator::make($request->all(), 
            empty($dump) ?
            ([strtolower($fieldType) => ($fieldType == 'email') ? 'required|email|unique:users' : 'required|unique:users|digits:10']): ([strtolower($fieldType) => ($fieldType == 'email') ? 'required|unique:users,email,'.$dump->id : 'required|unique:users,phone,'.$dump->id])
        ,$messages);

        if ($validator->fails()) {
            return response()->json(['error'=>$validator->errors()], 401);            
        }

        if(!empty($dump) && $dump->account_validity != 'temperory' ){
            return response()->json(['error'=>['account_recovery'=>['It seems your account is already verified or created previously, you can use other services to recover your account.']]], 401); 
        }

        if(!empty($dump) && $dump->expires_at > time()){
            return response()->json(['error'=>['otp_expires'=>['Your otp doesn\'t expired yet, please try again.']]], 401); 
        }

        //verify recaptcha
        if($request->debug == ''){
            if (verify_google_recaptcha($request->recaptcha) === false) {
                return response()->json(['error'=>['recaptcha'=>['Recaptcha verification failed']]], 401); 
            }
        }
        $otp = mt_rand(100000, 999999);
        $message ='Your OTP is '.$otp;

        if ($fieldType == 'email') {
            try{
                Mail::to($request->input)->send(new CustomerVerification($otp));
                if (!Mail::failures()) {

                    $user = $this->create_user_after_send_otp($fieldType,$otp,$request,$dump);

                    return response()->json([
                        'success' => 200,
                        'message' =>'An OTP has been send on your mail, please check and confirm, thank you.',
                        'results' => [
                            'hash_key' => $user->id,
                            
                        ]
                    ]);
                }else{
                    return response()->json([
                        'success' => 401,
                        'message' =>'Something went wrong while sending the mail, please check and try again later.'
                    ],401);    
                }
            }catch(\Swift_TransportException $e){
                return response()->json([
                    'success' => 401,
                    'message' =>$e->getMessage()
                ],401); 
            }
        }else{
            $response  = send_message($request->input,$message);
            if($response['response_code'] != 1002){
                
                $user = $this->create_user_after_send_otp($fieldType,$otp,$request,$dump);

                return response()->json([
                    'success' => 200,
                    'message' =>'An OTP has been send on your phone, please check and confrim, thank you.',
                    'results' => [
                                'hash_key' => $user->id
                            ]
                ]);
            }else{
                return response()->json([
                    'success' => false,
                    'message' =>$response['response']
                ],401);               
            }
        }
    }

    public function create_user_after_send_otp($fieldType,$otp,$request,$dump){
        $user = !empty($dump) ? $dump : new User();
        if(empty($dump)){
            $user->$fieldType = $request->get('input');
            $token = Str::random(60);
            $user->api_token = hash('sha256', $token);
            $user->setMeta('account_validity', 'temperory');
            $user->save();
            $user
            ->roles()
            ->attach(Role::where('name',($request->role !='' ? $request->role : 'customer'))->first());
            $user->setMeta('register_type', $fieldType);
        }
        $user->otp = $otp;
        $user->setMeta('expires_at', strtotime("+10 second"));
        $user->save(); 
        return $user;       
    }

    /**
     * Complete registration
     * Checks phone and used to send the message
     * @Role: API
     * @param  \Illuminate\Http\Request  $request
     * @return JSON
     */
    public function user_verification_verifyotp_complete_registration(Request $request)
    {
         if($request->isMethod('post')){

            $fieldType = filter_var( $request->input, FILTER_VALIDATE_EMAIL ) ? 'email' : 'phone';
            $where = "where".ucfirst($fieldType);
            $user = User::$where($request->input)->first();

            if(empty($user)){
                return response()->json(['error'=>['account_error'=>['Invalid account.']]], 401); 
            }
            if(!empty($user) && $user->account_validity == 'permanent' ){
                return response()->json(['error'=>['account_recovery'=>['It seems your account is already verified or created previously.']]], 401); 
            }

            if($user->register_type != $fieldType){
                return response()->json(['error'=>['failed'=>['Invalid access, you can\'t use other input type while registration.']]], 401); 
            }

            if($user->otp == $request->otp) {
                if($user->status == 'Inactive' && $user->is_email_verified == "No"){
                    $request->merge([strtolower($fieldType) =>$request->input]);
                    $validator = Validator::make($request->all(), [
                        'otp' => 'required|min:6',
                        'first_name' => 'required|alpha|min:2|max:255',
                        'last_name' => 'required|alpha|min:2|max:255',              
                        $fieldType => 'required|unique:users,'.$fieldType.','.$user->id,
                        'password' => 'required|min:6',
                        'recaptcha'=>$request->debug == '' ? 'required' : ''                
                    ]);
                    if ($validator->fails()) {
                        return response()->json(['error'=>$validator->errors()], 401);            
                    }
                    //verify recaptcha
                    if($request->debug == ''){
                        if (verify_google_recaptcha($request->recaptcha) === false) {
                            return response()->json(['error'=>['recaptcha'=>['Recaptcha verification failed']]], 401); 
                        }
                    }
                    $user->first_name = $request->get ('first_name');
                    $user->last_name = $request->get ('last_name');
                    $user->email = $request->get ('email');
                    $user->password = Hash::make ($request->get ('password'));
                    $user->phone = $request->get ('phone');                  
                    $user->status = 'Active';
                    $user->is_email_verified = 'Yes';
                    $user->otp = null;
                    $user->setMeta('dob',  $request->dob);
                    $user->setMeta('gender',  $request->gender);                    
                    $user->setMeta('account_validity', 'permanent');                    
                    $user->save();

                    if($fieldType == 'email' && $user->email){
                        Mail::to($user->email)->send(new CustomerRegistered($user));                     
                        if (!Mail::failures()) {
                            return response()->json([
                                'success' => 200,
                                'message' =>'Verification successfull, thank you.',
                            ]);
                        }else{
                            return response()->json([
                                'success' => 400,
                                'message' =>'Something went wrong while sending the mail, please check and try again later.'
                            ],400);                
                        }
                    }else{
                        $message ="Hi ".$user->full_name.",\nWelcome to Klothus.\nThank you for being with us, You account has been verified.
                            \nThank You,\nKlothus.com";
                        send_message($request->input,$message);
                        return response()->json([
                                'success' => 200,
                                'message' =>'Verification successfull, thank you.',
                        ]);
                    }
                }else{
                    return response()->json([
                        'success' => 400,
                        'message' =>'Your account is already verified.'
                    ],400);                   
                }
            }else{
                return response()->json([
                    'success' => 400,
                    'message' =>'Invalid otp, please try again.'
                ],400);                    
            }
        }        
    }
}