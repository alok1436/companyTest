<?php

namespace App\Http\Controllers\Class;

use DB;
use File;
use Hash;
use Auth;
use App\Role;
use App\User;
use App\RoleRequest;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use App\Mail\Vendor\VendorRegistered;
use Illuminate\Support\Facades\Validator;

class DeliveryController extends Controller
{
    public $request;

    public function upgrade(Request $request){
        $user = Auth::user();

        if(request()->isMethod('post')){
            $user = Auth::User();
            if(!$user->hasRole($request->role)){
                if($request->role == 'customer'){
                    $user->roles()->attach(Role::where('name',$request->role)->first());
                    $this->manage_role_histroy($user, $request);
                    return response()->json([
                        'success' => 200,
                        'message' =>'Now you became a '.$request->role.' and now we\'re reviewing your request you will be get notified soon.'
                    ]);

                }else if($request->role == 'vendor'){
        
                    $messages = [
                      'reg_company_name.required' => 'Registered Company Name field is required.',
                      'registration_number.required' => 'Registered Number field is required.',
                    ];
                    $validator = Validator::make($request->all(), [
                        'reg_company_name' => 'required',
                        'registration_number' => 'required',
                        'phone' => 'required|unique:users,phone,'.$user->id,
                        'email' => 'required|unique:users,email,'.$user->id,
                        //'shop_open_date:required|Y-m-d|before:tomorrow',
                    ],$messages);
                    if ($validator->fails()) {
                        return response()->json(['error'=>$validator->errors()], 401);            
                    } 

                    $this->request = $request;

                    if($user->getMeta('registration_number') != $request->registration_number){
                        $registration_number_meta = User::meta()
                            ->where(function($query){
                                  $query->where('users_meta.key', '=', 'registration_number')
                                        ->where('users_meta.value', '=',  $this->request->registration_number);
                            })->get();
                         if($registration_number_meta->count() > 0){
                             return response()->json(['error'=>['registration_already_exitsts'=>['Registration Number is already exits.']]], 401);  
                         }
                     }

                    if($user->getMeta('business_name') != $request->business_name){
                        $business_name_meta = User::meta()
                            ->where(function($query){
                                $query->where('users_meta.key', '=', 'business_name')
                                        ->where('users_meta.value', '=',  $this->request->business_name);
                            })->get();

                        if($business_name_meta->count() > 0){
                            return response()->json(['error'=>['business_name_already_exitsts'=>['Business name is already exits.']]], 401);  
                        }
                    }
                    //end Validation
                    $user->first_name = $request->get ( 'first_name' );
                    $user->last_name = $request->get ( 'last_name' );
                    $user->phone = $request->get ( 'phone' );
                    $user->email = $request->get ( 'email' );
                    if(trim($request->get ('password')) !=''){
                        $user->password = Hash::make ( $request->get ('password') );
                    }  
                    $user->save();

                    $user->setMeta('registration_number',  $request->registration_number);

                    if($request->hasFile('certficate_images')){
                        $certficate_images = $request->file('certficate_images');
                        $file_name = 'certficateImage_'.time().'.'.$certficate_images->getClientOriginalExtension();
                        $destinationPath = public_path('/images/certficates');
                        if (! File::exists($destinationPath)){
                            File::makeDirectory( $destinationPath );
                        }
                        $certficate_images->move( $destinationPath, $file_name ); 
                        $user->setMeta('certficate_images',  'images/certficates/'.$file_name); 
                    }
                    $user->setMeta('reg_company_name',  $request->reg_company_name);
                    $user->setMeta('location',  $request->location);
                    $user->setMeta('register_personal',  $request->register_personal);
                    $user->setMeta('representative',  $request->representative);
                    $user->setMeta('business_name',  $request->business_name);
                    $user->setMeta('business_type',  $request->business_type);
                    $user->setMeta('shop_open_date',  $request->shop_open_date);
                    $user->setMeta('brand_id',  $request->brand_id);
                    $user->setMeta('commission',  $request->commission);
                    $user->setMeta('categiry_id',  $request->get('categiry_id'));

                    if($request->hasFile('banners')){
                        $banners = $request->file('banners');
                        $file_name = 'banner_'.time().'.'.$banners->getClientOriginalExtension();
                        $destinationPath = public_path('/images/banners');
                        if (! File::exists($destinationPath)){
                            File::makeDirectory( $destinationPath );
                        }
                        $banners->move( $destinationPath, $file_name ); 
                        $user->setMeta('banners',  'images/banners/'.$file_name); 
                    }

                    if($request->hasFile('company_logo')){
                        $company_logo = $request->file('company_logo');
                        $file_name = 'company_logo_'.time().'.'.$company_logo->getClientOriginalExtension();
                        $destinationPath = public_path('/images/companyLogos');
                        if (! File::exists($destinationPath)){
                            File::makeDirectory( $destinationPath );
                        }
                        $company_logo->move( $destinationPath, $file_name ); 
                        $user->setMeta('company_logo',  'images/companyLogos/'.$file_name); 
                    }
                    $user->setMeta('dob',  $request->dob);
                    $user->setMeta('gender',  $request->gender);
                    $user->setMeta('address',  $request->address);
                    $user->save();
                    $user
                    ->roles()
                    ->attach(Role::where('name', 'vendor')->first());     


                    $this->manage_role_histroy($user, $request);
                    return response()->json([
                        'success' => 200,
                        'message' =>'Now you became a '.$request->role.' and now we\'re reviewing your request you will be get notified soon.'
                    ]);

                }
            }else{
                $role  = Role::where('name',$request->role)->first();
                $count = RoleRequest::where(['user_id'=>Auth::id(),'requested_role_id'=>$role->id,'status'=>'Pending'])->count();
                if($count == 1){
                    return response()->json([
                        'success' => 400,
                        'message' =>'We\'re reviewing your request you will be get notified soon.'
                    ],400);
                }else{
                    return response()->json([
                        'success' => 400,
                        'message' =>'This role already assigned to you'
                    ],400);
                }
            }
            //$user->roles()->sync(Role::where('name','vendor')->first());
            return response()->json([
                'success' => 400,
                'message' =>'Bad request'
            ],400); 
        }

        return view('delivery.upgrade',compact('user'));
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

    public function profile(Request $request)
    {
        //Update Delivery profile
        $user = User::where('id',Auth::User()->id)->get()->first();
        if (empty($user)) return abort(404);
        if( $request->isMethod('post') && $request->ajax()){

            //Start Validation
            $messages = [
              'first_name.required' => 'First Name field is required.',
              'last_name.required' => 'Last Name field is required.',
            ];
            $validator = Validator::make($request->all(), [
                'first_name' => 'required',
                'last_name' => 'required',
                'phone' => 'required|unique:users,phone,'.$user->id,
                'email' => 'required|unique:users,email,'.$user->id,
            ],$messages);
            if ($validator->fails()) {
                return response()->json(['error'=>$validator->errors()], 401);            
            } 

            //end Validation
            $user->first_name = $request->get('first_name');
            $user->last_name = $request->get('last_name');
            $user->email = $request->get('email');

            if($request->get('password')!=''){
                $user->password = Hash::make($request->get('password'));
            }

            $user->phone = $request->get('phone');

            if($request->hasFile('user_profile')){
                $user_profile = $request->file('user_profile');
                $file_name = 'img'.time().'.'.$user_profile->getClientOriginalExtension();
                $destinationPath = public_path('/images/users');
                if (! File::exists($destinationPath)){
                    File::makeDirectory( $destinationPath );
                }
                $user->user_profile = '/images/users/'.$file_name;
                $user_profile->move( $destinationPath, $file_name ); 
            }

            $user->status = $request->get ( 'status' );
            $user->save();

            $user->setMeta('joining_date',  $request->joining_date);
            $user->setMeta('dob',  $request->dob);
            $user->setMeta('gender',  $request->gender);
            $user->setMeta('current_address',  $request->current_address);
            $user->setMeta('permanent_address',  $request->permanent_address);
            $user->save();
            
            return response()->json([
                    'success' => 200,
                    'data'   => [],
                    'message'   =>'Delivery profile has been updated successfully.',
                    'redirect_url'   =>url('delivery/profile')
            ]);
        }
        return view('delivery.profile',compact('user'));
    }
}
