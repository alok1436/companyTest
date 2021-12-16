<?php
namespace App\Http\Controllers\API;
  
use Session;
use App\Role;
use App\User;
use App\Order;
use Validator;
use App\Company;
use App\Enquiry;
use App\Product;
use Illuminate\Support\Str;
use Illuminate\Http\Request; 
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth; 
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use App\Mail\Vendor\VendorRegistered;
use App\Mail\Vendor\VendorVerification;
use Illuminate\Support\Facades\Password;

class VendorApiController extends Controller
{

    public $request;
    /**
     * Vendor Registration new user
     * @Role: API
     * @param  \Illuminate\Http\Request  $request
     * @return JSON
     */
    public function verify_otp(Request $request){
        
        if( $request->isMethod('post')){

            $messages = [
            'input.required' => 'Email or phone required.',
            'otp' => 'required',
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

                if ($user->otp == $request->otp) {

                    $user->status = 'Inactive';
                    $user->is_email_verified = 'Yes';
                    $user->otp = null;
                    $user->save();
                    Mail::to($user->email)->send(new VendorRegistered($user));

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
                    'message' =>'Invalid vendor.'
                ],400);  
            }
        }
    }
    /**
     * Vendor Registration new user
     * @Role: API
     * @param  \Illuminate\Http\Request  $request
     * @return JSON
     */
    public function VerifyNregisterVendor(Request $request){

        if( $request->isMethod('post')){
           
            $validator = Validator::make($request->all(), [
                'first_name' => 'required|alpha|min:2|max:255',
                'last_name' => 'required|alpha|min:2|max:255',              
                'email' => 'required|email|unique:users',
                'phone' => 'required|unique:users|digits:10',
                'password' => 'required|min:6',
                'registration_number' => 'required',
                'business_name' => 'required',
                'business_type' => 'required',
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
                      $query->where('users_meta.key', '=', 'business_name')
                            ->where('users_meta.value', '=',  $this->request->business_name );
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
                    'message' =>'Business type is already exits.'
                ],400);     
             }
             
            $user = new User();
            $user->first_name = $request->get ('first_name');
            $user->last_name = $request->get ('last_name');
            $user->phone = $request->get ('phone');
            $user->email = $request->get ('email');
            $user->is_email_verified = 'No';
            $user->password = Hash::make ($request->get ('password'));
            $token = Str::random(60);
            $user->api_token = hash('sha256', $token);
            $user->save();
            $user
            ->roles()
            ->attach(Role::where('name', 'vendor')->first());
            
            $user->setMeta('registration_number',  $request->registration_number);
            $user->setMeta('location',  $request->location);
            $user->setMeta('register_personal',  $request->register_personal);
            $user->setMeta('representative',  $request->representative);
            $user->setMeta('business_type',  $request->business_type);
            $user->setMeta('shop_open_date',  $request->shop_open_date);
            $user->setMeta('brand_id',  $request->brand_id);
            $user->setMeta('categiry_id',  $request->get('categiry_id'));
            $user->setMeta('address',  $request->address);
            $user->setMeta('from',  $request->from);
            $user->save();

            //send mail to vendor after complete the registration
            $otp = mt_rand(100000, 999999);
            Mail::to($user->email)->send(new VendorVerification($otp));
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
                    'success' => 400,
                    'message' =>'Something went wrong while sending the mail, please check and try again later.'
                ],400);                
            }
        }
    }

     public function edit(){
            $vendor = auth('api')->user();
            if(!empty($vendor)){
            if($vendor->roles()->get()->first()->name == 'vendor'){
                return response()->json([
                    'success' => true,
                    'results'    => $vendor,
                    'message' =>'vendor edited.'
                ]);
             }else{
                 return response()->json([
                    'success' => 400,
                    'message' =>'invaild user!'
                ],400);  
             }
        }else{
           return response()->json([
                    'success' => 400,
                    'message' =>'invaild user id!'
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
                'password' => 'min:6',
            ]);
            if ($validator->fails()) {
                return response()->json(['error'=>$validator->errors()], 401);            
            } 
            $user = auth('api')->user();
            $user->first_name = $request->get ( 'first_name' );
            $user->last_name = $request->get ( 'last_name' );
            //$user->phone = $request->get ( 'phone' );
            //$user->email = $request->get ( 'email' );
            if(!empty(request()->password)){
               $user->password = Hash::make ( $request->get ( 'password' ) );
            }
           
            $user->save();

            $user->setMeta('registration_number',  $request->registration_number);
            $user->setMeta('location',  $request->location);
            $user->setMeta('register_personal',  $request->register_personal);
            $user->setMeta('representative',  $request->representative);
            $user->setMeta('business_type',  $request->business_type);
            $user->setMeta('shop_open_date',  $request->shop_open_date);
            $user->setMeta('brand_id',  $request->brand_id);
            $user->setMeta('categiry_id',  $request->get('categiry_id'));
            $user->setMeta('address',  $request->address);
            $user->setMeta('dob',  $request->dob);
            $user->setMeta('gender',  $request->gender);
            $user->save();

            return response()->json([
                'success' => 200,
                'data'    => ['token'=>$user->api_token],
                'message' =>'Account has been updated.'
            ]);
        }
        return response()->json([
            'success' => 400,
            'message' =>'Bad request'
        ],401);
    }

    public function getProductByVendorId($id){
        $product = Product::with('author')->where('vendor_id',$id)->get();
         
        return response()->json([
            'success' => true,
            'results'    => $product,
            'message' =>'product get successfull.'
        ]);
    }
/*
    public function orders(Request $request){
        $orders = auth('api')->user()->shopOrders()->paginate(10);
        return response()->json([
            'success' => true,
            'results'    => $orders,
            'message' =>'Orders Loaded'
        ]);
    }*/

    /**
     * Dispatch orders
     * @Role: API
     * @param  \Illuminate\Http\Request  $request
     * @return JSON
     */
    public function orders(Request $request){
        if( $request->isMethod('post')){
            
            $validator = Validator::make($request->all(), [
                'status'=> 'required'
            ]);

            if ($validator->fails()) {
                return response()->json(['error'=>$validator->errors()], 400);            
            }    
            $collection = auth('api')->user()->shopOrders();

            if(isset($request->from_date) && $request->from_date !='' && isset($request->to_date) && $request->to_date !=''){
            $collection->whereBetween('orders.created_at', [$request->from_date." 00:00:00", $request->to_date." 23:59:59"]);

            }else if(isset($request->from_date) && $request->from_date !=''){
                $collection->whereDate('orders.created_at',$request->from_date);

            }else if(isset($request->to_date) && $request->to_date !=''){
                $collection->whereDate('orders.created_at',$request->to_date);
            }

            if(request()->brand >0){
                $collection->whereHas('item.brands', function($q){
                    $q->whereBrandId(request()->brand);  
                });
            }

            if($request->sub_order_code !=''){
                $collection->where('sub_order_code',$request->sub_order_code)->orwhere('order_code',$request->sub_order_code);
            }           

            if($request->get('status')){
                $collection->where('status',$request->get('status'));
            }

            $orders = $collection->orderBy('id','DESC')->get();

            return response()->json([
                'success' => true,
                'results'    => $orders,
                'message' =>'Orders loaded.'
            ]);
        }
    }

    /**
     * Update status 
     * Ristrict ONlt vendor
     * @Role: API
     * @param  \Illuminate\Http\Request  $request
     * @return JSON
     */
    public function update_status(Request $request)
    {
         if( $request->isMethod('post')){
            $validator = Validator::make($request->all(), [
                'id' => 'required|exists:App\Order,id',
                'status'=> 'required',
                'reason' => $request->get('status') == 'Cancelled' ? 'required|min:10' :'',
            ]);

            if ($validator->fails()) {
                return response()->json(['error'=>$validator->errors()], 401);            
            }

            $order = auth('api')->user()->shopOrders()->where('id', $request->get('id'))->first();
            if($order){
                $order->status = $request->get('status');
                $order->save();

                if($request->get('status') == 'Cancelled'){
                    $order->setMeta('reason',$request->get('reason'));
                    $order->save();
                }
                return response()->json([
                    'success' => 200,
                    'results'    => [],
                    'message' => 'Thank you, order status has been changed.'
                ]);
            }else{
                return response()->json([
                    'success' => 400,
                    'message' =>'Invalid order request, only that guys can take action who get assigned this order, thanks.'
                ]); 
            }
        }       
    }
    public function inquiry(Request $request){
       if(request()->isMethod('post')){

        $validator = Validator::make($request->all(), [
                'name_brand' => 'required',
                'phone' => 'required|unique:enquiries',
                'merchent_name' => 'required',
                'shop_location' => 'required',
                'made_in' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json(['error'=>$validator->errors()], 401);            
            } 
              $enquiry = new Enquiry;
              $enquiry->name = request()->name_brand;
              $enquiry->merchent_name = request()->merchent_name;
              $enquiry->phone = request()->phone;
              $enquiry->shop_location = request()->shop_location;
              $enquiry->made_in_nepal = request()->made_in;
              $enquiry->save();
              return response()->json([
                'success' => 200,
                'data'    => [],
                'message' =>'Enquiry has been send successfull!.'
            ]);
       }
    }


    public function analytics(Request $request){
            $user = auth('api')->user();
            if($user->roles()->first()->name == 'vendor'){
                $results['total_order_count'] = $user->shopOrders->count();
                $results['total_sale_completed'] = $user->shopOrders()->where('status','Completed')->sum('item_total');
                $results['total_sale_pending'] = $user->shopOrders()->where('status','Pending')->sum('item_total');
                $results['total_commission'] = $user->shopOrders()->where('status','Completed')->sum('commission');
                $results['latest_order'] =  Order::with('customer')->orderBy('id', 'DESC')->where(['status'=>'pending','vendor_id'=>$user->id])->paginate(10);
                return response()->json([
                'success' => 200,
                'data'    => $results,
                'message' =>'Analytics loaded'
             ]);

            }else{
                return response()->json([
                    'success' => 400,
                    'message' =>'Only vendors can access'
                ],400);
            }
    }

        /**
     * get order
     * @Role: API
     * @param  \Illuminate\Http\Request  $request
     * @return JSON
     */
    public function get(Request $request){
        $order = auth('api')->user()->shopOrders()->with('mainOrder')->where('sub_order_code',$request->subordercode)->first();
        return response()->json([
            'success' => true,
            'results'    => $order,
            'message' =>'Order loaded.'
        ]);
    }
}