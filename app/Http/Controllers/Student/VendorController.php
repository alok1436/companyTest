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
use App\ProductToCategory;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use App\Mail\Vendor\VendorRegistered;
use Illuminate\Support\Facades\Validator;

class VendorController extends Controller
{
    public $request;

    public function profile(Request $request)
    {
        //Update Vendor profile
        $user = User::where('id',Auth::User()->id)->get()->first();
        // $userMeta = DB::table('users_meta')->where('user_id',$user->id)->get();
        // dd($userMeta);
        if (empty($user)) return abort(404);
        if( $request->isMethod('post') && $request->ajax()){

            //Start Validation
            $messages = [
              'name.required' => 'Registered Company Name field is required.',
              'name.unique' => 'Registered Company Name already exits.',
              'registration_number.required' => 'Registered Number field is required.',
            ];
            $validator = Validator::make($request->all(), [
                'name' => 'required|unique:users,first_name,'.$user->id,
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
            //end Validation
            $user->first_name = $request->get('name');
            $user->email = $request->get('email');
            if($request->get('password')!=''){
                $user->password = Hash::make($request->get('password'));
            }
            $user->phone = $request->get('phone');
            //$user->status = $request->get ( 'status' );
            //$user->is_email_verified = $request->get ( 'is_email_verified' );
            $user->save();
            $user->setMeta('registration_number',  $request->registration_number);
            $user->setMeta('location',  $request->location);
            $user->setMeta('register_personal',  $request->register_personal);
            $user->setMeta('representative',  $request->representative);
            $user->setMeta('business_name',  $request->business_name);
            $user->setMeta('business_type',  $request->business_type);
            $user->setMeta('shop_open_date',  $request->shop_open_date);
            $user->setMeta('brand_id',  $request->brand_id);
            //$user->setMeta('dob',  $request->dob);
            $user->setMeta('gender',  $request->gender);
            $user->setMeta('address',  $request->address);
            $user->setMeta('from', 'Website');
            $user->categories()->sync($request->categiry_id);
            
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
            $user->save();
            
            return response()->json([
                    'success' => 200,
                    'data'   => [],
                    'message'   =>'Vendor profile has been updated successfully.',
                    'redirect_url'   =>url('vendor/profile')
            ]);
        }
        $brands = Brand::pluck('name','id');
        $categories = Category::pluck('name','id');
        $categories->prepend('Select Category',0);
        return view('vendor.vendors.profile',compact('user','brands','categories'));
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
