<?php
namespace App\Http\Controllers\API;
  
use Session;
use App\Role;
use App\User;
use App\MainOrder;
use App\Order;
use Validator;
use App\Company;
use App\Affiliate;
use App\RoleRequest;
use App\Notification;
use Illuminate\Support\Str;
use Illuminate\Http\Request; 
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth; 
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use App\Mail\Customer\CustomerRegistered;
use App\Mail\Customer\CustomerVerification;
use Illuminate\Support\Facades\Password;

class CustomerApiController extends Controller
{
    /**
     * Customer verification
     * @Role: API
     * @param  \Illuminate\Http\Request  $request
     * @return JSON
     */
    public function verify_otp(Request $request){
        
        if( $request->isMethod('post')){
            $messages = [
            'input.required' => 'Email or phone required.',
            'otp' => 'required'
            ]; 

            $fieldType = filter_var( $request->input, FILTER_VALIDATE_EMAIL ) ? 'Email' : 'Phone';

            $validator = Validator::make($request->all(), [
                'input' => ($fieldType == 'Email') ? 'required|email' : 'required',
            ],$messages);

            $where = "where".$fieldType;
            $user = User::$where($request->input)->first();

            if ($user) {
                if ($user->otp == $request->otp) {
                    $user->status = 'Active';
                    $user->is_email_verified = 'Yes';
                    $user->otp = null;
                    $user->save();
                    
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
                    return response()->json([
                        'success' => 400,
                        'message' =>'Invalid otp, please try again.'
                    ],400);                    
                }
            }else{
                return response()->json([
                    'success' => 400,
                    'message' =>'Invalid Customer.'
                ],400);  
            }
        }
    }

    /**
    * Customer Registration new user
    * @Role: API
    * @param  \Illuminate\Http\Request  $request
    * @return JSON
    */
    public function registration(Request $request){
        if( $request->isMethod('post')){
            //Start Validation
            $messages = [
              'first_name.required' => 'First name field is required.', 
              'last_name.required' => 'Last name field is required.', 
            ];

            $validator = Validator::make($request->all(), [
                'first_name' => 'required|alpha|min:2|max:255',
                'last_name' => 'required|alpha|min:2|max:255',              
                'email' => 'required|email|unique:users',
                'phone' => 'required|unique:users|digits:10',
                'password' => 'required|min:6',
                'recaptcha'=>$request->debug == '' ? 'required' : ''
            ],$messages);

            if ($validator->fails()) {
                return response()->json(['error'=>$validator->errors()], 401);            
            }
            //end Validation
            //verify recaptcha
            if($request->debug == ''){
                if (verify_google_recaptcha($request->recaptcha) === false) {
                    return response()->json(['error'=>['recaptcha'=>['Recaptcha verification failed']]], 401); 
                }
            }

            $user = new User();
            $user->first_name = $request->get ('first_name');
            $user->last_name = $request->get ('last_name');
            $user->email = $request->get ('email');
            $user->password = Hash::make ($request->get ('password'));
            $user->phone = $request->get ('phone');
            $user->dob = $request->get ('dob');
            $user->gender = $request->get ('gender');
            
            $token = Str::random(60);
            $user->api_token = hash('sha256', $token);
            $user->save();
            $user
            ->roles()
            ->attach(Role::where('name',($request->role !='' ? $request->role : 'customer'))->first());
            
            $user->setMeta('dob',  $request->dob);
            $user->setMeta('gender',  $request->gender);
            $user->setMeta('from',  $request->from);
            $user->save();

            //send mail to vendor after complete the registration
            $otp = mt_rand(100000, 999999);
            Mail::to($user->email)->send(new CustomerVerification($otp));
            $message ='Your Registration OTP Code is :-'.$otp;
            $response  = send_message($user->phone,$message);
            $user->otp = $otp;
            $user->save();

            if (!Mail::failures()) {
                return response()->json([
                    'success' => 200,
                    'message' =>'An OTP has been send on your mail, please check and proceed further for complete registation, thank you.',
                    'results' => []
                ]);
            }else{
                return response()->json([
                    'success' => false,
                    'message' =>'Something went wrong while sending the mail, please check and try again later.'
                ],400);                
            }
        }
    }

    public function assignRole(Request $request){
        $user = auth('api')->user();
        if(!$user->hasRole($request->role)){
            if($request->role == 'customer'){
                $user->roles()->attach(Role::where('name',$request->role)->first());
                $this->manage_role_histroy($user, $request);
                return response()->json([
                    'success' => 200,
                    'message' =>'Now you became a '.$request->role.' and now we\'re reviewing your request you will be get notified soon.'
                ]);

            }else if($request->role == 'vendor'){
            $validator = Validator::make($request->all(), [
                'registration_number' => 'required',
                'location' => 'required',
                'register_personal' => 'required',
                'representative' => 'required',
                'business_type' => 'required',
                'representative' => 'required',
                'address' => 'required',
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

                $this->request = $request;
                $reg_no_meta = User::meta()
                    ->where(function($query){
                          $query->where('users_meta.key', '=', 'registration_number')
                                ->where('users_meta.value', '=',  $this->request->registration_number );
                    })->get();
                $business_type_meta = User::meta()
                    ->where(function($query){
                          $query->where('users_meta.key', '=', 'business_type')
                                ->where('users_meta.value', '=',  $this->request->business_type );
                    })->get();

                 if($reg_no_meta->count() > 0){
                       return response()->json([
                        'success' => 400,
                        'message' =>'Registration Number is already exits.'
                    ],400);     
                 }
                 if($business_type_meta->count() > 0){
                       return response()->json([
                        'success' => 400,
                        'message' =>'Business Name is already exits.'
                    ],400);     
                 }   
                $role = Role::where('name',$request->role)->first();                         
                $user->setMeta('registration_number',  $request->registration_number);
                $user->setMeta('location',  $request->location);
                $user->setMeta('register_personal',  $request->register_personal);
                $user->setMeta('representative',  $request->representative);
                $user->setMeta('business_type',  $request->business_type);
                $user->setMeta('shop_open_date',  $request->shop_open_date);
                $user->setMeta('brand_id',  $request->brand_id);
                $user->setMeta('categiry_id',  $request->get('categiry_id'));
                $user->setMeta('address',  $request->address);
                $user->save();
                $user->roles()->attach($role);
                $this->manage_role_histroy($user, $request);
                return response()->json([
                    'success' => 200,
                    'message' =>'Now you became a '.$request->role.' and now we\'re reviewing your request you will be get notified soon.'
                ]);
            }else if($request->role == 'delivery'){
                $user->roles()->attach(Role::where('name',$request->role)->first());
                $this->manage_role_histroy($user, $request);
                return response()->json([
                    'success' => 200,
                    'message' =>'Now you became a '.$request->role.' and now we\'re reviewing your request you will be get notified soon.'
                ]);

            }
        }else{
            return response()->json([
                'success' => 400,
                'message' =>'This role already assigned to you'
            ],400);
        }
        //$user->roles()->sync(Role::where('name','vendor')->first());
        return response()->json([
            'success' => 200,
            'message' =>'Role has been assigned'
        ]);   
    }

    public function manage_role_histroy($user, $request){
            $role = Role::where('name',$request->role)->first();
            $user->setMeta('new_role_request', 1);
            $user->setMeta('approved_status','Unapproved');
            $user->setMeta('requested_role', $request->role);
            $user->save();
            
            $requested_role = new RoleRequest();
            $requested_role->user_id = $user->id;
            $requested_role->requested_role_id = $role->id;
            $requested_role->save();

            /*$total_admins = getAdmins();
                if($total_admins->count() > 0){
                      foreach ($total_admins as $admin){
                      $notification = new Notification();
                      $notification->title = 'New role request';
                      $notification->body = $user->full_name.' wants to become a '.$request->role;
                      $notification->user_id = $admin->id;
                      $notification->order_id = $role->id;
                      $notification->type = 'role_request';
                      $notification->data = json_encode($role);
                      if ( $notification->save() ) {
                        $notification->toSingleDevice($admin->device_token, $notification->title, $notification->body, $icon= null, strtoupper(uniqid()),$admin->id);
                    }
                }
            }*/
    }

    /**
    * Customer Registration new user
    * @Role: API
    * @param  \Illuminate\Http\Request  $request
    * @return JSON
    */
    public function edit(){
            $customer = auth('api')->user();
            if(!empty($customer)){
                if($customer->roles()->get()->first()->name == 'customer'){
                    return response()->json([
                        'success' => true,
                        'results'    => $customer,
                        'message' =>'Customer edited.'
                    ]);
                }else{
                     return response()->json([
                            'success' => 400,
                            'message' =>'invaild Customer!'
                        ],400);  
                }
            }else{
                 return response()->json([
                            'success' => 400,
                            'message' =>'invaild user!'
                        ],400); 
            }
        
    }

    public function update(Request $request){
        if( $request->isMethod('post')){
            $validator = Validator::make($request->all(), [
                'first_name' => 'required|alpha|min:2|max:255',
                'last_name' => 'required|alpha|min:2|max:255',
                //'phone' => 'required|unique:users,phone,'.auth('api')->user()->id,
                //'email' => 'required|unique:users,email,'.auth('api')->user()->id,
            ]);
            if ($validator->fails()) {
                return response()->json(['error'=>$validator->errors()], 401);            
            } 

            $user = auth('api')->user();
            $user->first_name = $request->get ( 'first_name' );
            $user->last_name = $request->get ( 'last_name' );
            //$user->phone = $request->get ( 'phone' );
            //$user->email = $request->get ( 'email' );
           
            $user->save();
            
            $user->setMeta('billing_address',  $request->billing_address);
            $user->setMeta('shipping_address',  $request->shipping_address);
            $user->setMeta('gender',  $request->gender);
            $user->setMeta('dob',  $request->dob);
            $user->save();

            return response()->json([
                'success' => 200,
                'message' =>'Profile has been updated.'
            ]);
        }
        return response()->json([
            'success' => 400,
            'message' =>'Bad request'
        ],401);
    }

    public function customer_order_by_id($id){
        
        $user = User::find($id);
        if($user){
            $order = Order::with('item')->where('customer_id',$id)->orderBy('id','desc')->get();
            return response()->json([
                'success' => true,
                'results' =>$order,
                'message' =>'Order has been loaded.'
            ]);
        }else{
            return response()->json([
                    'success' => 400,
                    'message' =>'Invalid customer.'
            ],400);
        } 
    }

    public function get_affiliations(){
        $affiliations = auth('api')->user()->affiliations;
        if($affiliations){      
            return response()->json([
                'success' => true,
                'results' =>$affiliations,
            ]);
        }else{
            return response()->json([
                    'success' => 400,
                    'message' =>'No data found.'
            ],400);
        } 
    }

    public function create_affiliate_request(Request $request){
        $validator = Validator::make($request->all(), [
                'id' => 'required|exists:App\AffiliateOrder,id'
        ]);
        if ($validator->fails()) {
            return response()->json(['error'=>$validator->errors()], 401);            
        }

        $user = auth('api')->user();
        $affiliate = $user->affiliations()->where('id',$request->affiliate_id)->first();
        
        if ($affiliate) {
            
            $affiliateOrder = $affiliate->affiliateOrder()->where('id',$request->id)->first();

            if($affiliateOrder){
                if($affiliateOrder->status == 'Completed'){

                    $affiliateOrder->requested = 1;
                    $affiliateOrder->save();
                    return response()->json([
                                'success' => 200,
                                'message' =>'Thank you, Request has been created.'
                        ],200);
                                          
                }else{
                    return response()->json([
                                'success' => 400,
                                'message' =>'This request cant be proceed because affiliation is in pending.'
                        ],400);                    
                }
            }else{
                return response()->json([
                        'success' => 400,
                        'message' =>'You have already requested for this affiliation, please try again.'
                ],400);
            }            
        }else{
            return response()->json([
                        'success' => 400,
                        'message' =>'Invalid affiliate.'
                ],400);
        }
    }
}