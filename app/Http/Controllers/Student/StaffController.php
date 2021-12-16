<?php

namespace App\Http\Controllers\Vendor;

use Hash;
use File;
use DateTime;
use App\User;
use App\Role;
use App\Staff;
use App\Brand;
use App\Category;
use App\SubCategory;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class StaffController extends Controller
{
    public const REDIRECT_URL = 'vendor/staffs';

    public function index(Request $request)
    {
        $staffs = User::whereHas('roles', function($q){ 
            $q->where('roles.name','staff');
        })->get();
        return view('vendor.staffs.index', compact('staffs'));
    }

    public function store(Request $request)
    {
        //Create Staff
        if( $request->isMethod('post') && $request->ajax()){

            //Start Validation
            $validator = Validator::make($request->all(), [
                'first_name' => 'required|alpha|min:2|max:255',
                'last_name' => 'required|alpha|min:2|max:255',
                'phone' => 'required|unique:users|digits:10',
                'email' => 'required|email|unique:users',
                'password' => 'required|min:6',
                'status'=>'required',
                'dob' =>'required|date|before:16 years ago',
            ]);
 
            $user = new User();
            $user->first_name = $request->get ( 'first_name' );
            $user->last_name = $request->get ( 'last_name' );
            $user->email = $request->get ( 'email' );
            $user->phone = $request->get ( 'phone' );
            $user->password = Hash::make ($request->get ('password'));
            $user->status = $request->get ( 'status' );
            $user->is_email_verified = 'Yes';
            $user->remember_token = $request->get ( '_token' );
            if($request->hasFile('profile'))
            {
                $profile = $request->file('profile');
                $file_name = 'img_'.time().'.'.$profile->getClientOriginalExtension();
                $destinationPath = public_path('/images/user');
                if (! File::exists($destinationPath)){
                    File::makeDirectory( $destinationPath );
                }
                $user->user_profile = '/images/user/'.$file_name;
                $profile->move( $destinationPath, $file_name );
            }
            $user->save();
            if($user){
                $user
                ->roles()
                ->attach(Role::where('name', 'staff')->first());
                
                $user->setMeta('dob',  $request->dob);
                $user->setMeta('department',  $request->department);
                $user->setMeta('position',  $request->position);
                $user->setMeta('address',  $request->address);
                $user->save();

                return response()->json([
                    'success' => 200,
                    'message' =>'Staff has been create successfully.',
                    'redirect_url' => url(self::REDIRECT_URL),
                    //'reload'=>false
                ]);
            }else{
                return response()->json([
                        'success' => 400,
                        'message' =>'Something went wrong.'
                ],400);  
            }
        }
        return view('vendor.staffs.store');
    }

    public function edit(Request $request, $id)
    {
        //Update Staff
        $user = User::find($id);
        if( $request->isMethod('post') && $request->ajax()){

            ///Start Validation
            $validator = Validator::make($request->all(), [
                'first_name' => 'required',
                'last_name' => 'required',
                'phone' => 'required|unique:users,phone,'.$user->id,
                'email' => 'required|unique:users,email,'.$user->id,
                'status'=>'required'
            ]);
            if ($validator->fails()) {
                return response()->json(['error'=>$validator->errors()], 401);            
            } 
            //end Validation

            $user->first_name = $request->get ( 'first_name' );
            $user->last_name = $request->get ( 'last_name' );
            $user->email = $request->get ( 'email' );
            $user->phone = $request->get ( 'phone' );
            if(trim($request->get ('password')) !=''){
                $user->password = Hash::make ( $request->get ('password') );
            }  
            $user->status = $request->get ( 'status' );
            $user->remember_token = $request->get ( '_token' );
            if($request->hasFile('profile'))
            {
                $profile = $request->file('profile');
                $file_name = 'img_'.time().'.'.$profile->getClientOriginalExtension();
                $destinationPath = public_path('/images/user');
                if (! File::exists($destinationPath)){
                    File::makeDirectory( $destinationPath );
                }
                $user->user_profile = '/images/user/'.$file_name;
                $profile->move( $destinationPath, $file_name );
            }
            $user->setMeta('dob',  $request->dob);
            $user->setMeta('department',  $request->department);
            $user->setMeta('position',  $request->position);
            $user->setMeta('address',  $request->address);
            $user->save();

            return response()->json([
                'success' => 200,
                'message' =>'Staff has been update successfully.',
                'redirect_url' => url('vendor/staff/edit/'.$user->id),
                'reload'=>false
            ]);
        }
        
        return view('vendor.staffs.edit',compact('user'));
    }

    public function delete(Request $request, $id)
    {
        if(User::find($id)){
            User::find($id)->delete();
            return redirect()->back()->with('success', 'Staff deleted successfully');
        }else{
            return redirect()->back()->with('error', 'Faield');
        }
    }
}
