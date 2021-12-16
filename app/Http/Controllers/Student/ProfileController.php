<?php

namespace App\Http\Controllers\Vendor;

use DB;
use File;
use Hash;
use Auth;
use App\Role;
use App\User;
use App\Feedback;
use App\Brand;
use App\Company;
use App\Product;
use App\Category;
use App\SubCategory;
use App\RoleRequest;
use App\ProductToCategory;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use App\Mail\Vendor\VendorRegistered;
use Illuminate\Support\Facades\Validator;

class ProfileController extends Controller
{
    public $request;

    public function upgrade(Request $request)
    {   
        //Update Vendor profile
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

                }else if($request->role == 'delivery'){
                    $user->roles()->attach(Role::where('name',$request->role)->first());
                    $this->manage_role_histroy($user, $request);
                    return response()->json([
                        'success' => 200,
                        'message' =>'Now you became a '.$request->role.' and now we\'re reviewing your request you will be get notified soon.'
                    ]);

                }
            }else{
                $role = Role::where('name',$request->role)->first();
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
        return view('vendor.vendors.upgrade');
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

    public function feedback_store(Request $request){
           if(request()->isMethod('post')){

            $validator = Validator::make($request->all(), [
                'title' => 'required',
                'msg' => 'required'
            ]);
            if ($validator->fails()) {
                return response()->json(['error'=>$validator->errors()], 401);            
            } 
                $data = new Feedback;
                $data->vendor_id = Auth::user()->id;
                $data->title = request()->title;
                $data->message = request()->msg;
                $data->add_date = time();
                $data->save();
                return response()->json([
                    'success' => 200,
                    'message'   =>'feedback send successfully.'
            ]);
           }else{
            return view('vendor.feedback.feedback');
           }
    }
    public function updatePassword(Request $request)
    {   

        if( $request->isMethod('post')){
                $validator = Validator::make($request->all(), [
                    'new_password' => 'required|min:6',
                    'confirm_password' => 'required|same:new_password',
                ]);

                if ($validator->fails()) {
                    return response()->json(['error'=>$validator->errors()], 401);            
                } 

                //verify recaptcha
                /*if($request->debug == ''){
                    if (verify_google_recaptcha($request->recaptcha) === false) {
                        return response()->json(['error'=>['recaptcha'=>['Recaptcha verification failed']]], 401); 
                    }
                }*/

                $user = Auth::user();  
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
        return view('vendor.vendors.updatePassword');
        }
        
}
