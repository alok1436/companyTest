<?php

namespace App\Http\Controllers\Vendor;

use File;
use App\User;
use App\Role;
use App\Staff;
use App\Stock;
use App\Affilate;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class StockController extends Controller
{
    public const REDIRECT_URL = 'vendor/stocks';

    public function index(Request $request)
    {
        $stocks = Stock::get();
        return view('vendor.stocks.index', compact('stocks'));
    }

    public function store(Request $request)
    {
        //Create Staff
        if( $request->isMethod('post') && $request->ajax()){

            //Start Validation
            $messages = [
              'first_name.required' => 'Name field is required.', 
            ];
            $validator = Validator::make($request->all(), [
                'first_name' => 'required',
                'email' => 'required',
                'phone' => 'required',
                'dob' => 'required',
            ],$messages);
            if ($validator->fails()) {
                return response()->json(['error'=>$validator->errors()], 401);            
            } 
            //end Validation

            $user = new User();
            $user->first_name = $request->get ( 'first_name' );
            $user->last_name = $request->get ( 'last_name' );
            $user->email = $request->get ( 'email' );
            $user->phone = $request->get ( 'phone' );
            $user->remember_token = $request->get ( '_token' );
            $user->save();
            $user
            ->roles()
            ->attach(Role::where('name', 'vendor')->first());

            $staff = new Staff();
            $staff->user_id = $user->id;
            foreach($request->all() as $key=>$value){
                if( in_array( $key,$staff->getFillable() ) ){
                    $staff->$key = $value;
                }
            }  

            if($request->hasFile('profile'))
            {
                $profile = $request->file('profile');
                $file_name = 'staff_'.time().'.'.$profile->getClientOriginalExtension();
                $destinationPath = public_path('/images/staffs');
                if (! File::exists($destinationPath)){
                    File::makeDirectory( $destinationPath );
                }
                $staff->profile = '/images/staffs/'.$file_name;
                $profile->move( $destinationPath, $file_name );
            }
            $staff->save();

            return response()->json([
                    'success' => true,
                    'data'   => [],
                    'message'=>'staff has been created.',
                    'redirect_url'   =>url(self::REDIRECT_URL)
            ]);
        }
        return view('vendor.staffs.store');
    }

    public function edit(Request $request, $id)
    {
        //Update Staff
        $staff = Staff::find($id);
        if( $request->isMethod('post') && $request->ajax()){

            //Start Validation
            $messages = [
              'first_name.required' => 'Name field is required.', 
            ];
            $validator = Validator::make($request->all(), [
                'first_name' => 'required',
                'email' => 'required',
                'phone' => 'required',
                'dob' => 'required',
            ],$messages);
            if ($validator->fails()) {
                return response()->json(['error'=>$validator->errors()], 401);            
            } 
            //end Validation

            $user = User::find($staff->user_id);
            $user->first_name = $request->get ( 'first_name' );
            $user->last_name = $request->get ( 'last_name' );
            $user->email = $request->get ( 'email' );
            $user->phone = $request->get ( 'phone' );
            $user->remember_token = $request->get ( '_token' );
            $user->save();

            foreach($request->all() as $key=>$value){
                if( in_array( $key,$staff->getFillable() ) ){
                    $staff->$key = $value;
                }
            } 
            
            if($request->hasFile('profile'))
            {
                $profile = $request->file('profile');
                $file_name = 'staff_'.time().'.'.$profile->getClientOriginalExtension();
                $destinationPath = public_path('/images/staffs');
                if (! File::exists($destinationPath)){
                    File::makeDirectory( $destinationPath );
                }
                $staff->profile = '/images/staffs/'.$file_name;
                $profile->move( $destinationPath, $file_name );
            }
            $staff->save();

            return response()->json([
                    'success' => true,
                    'data'   => [],
                    'message'=>'Staff has been updated.',
                    'redirect_url'   =>url('vendor/staff/edit/'.$staff->id)
            ]);
        }
        
        return view('vendor.staffs.edit',compact('staff'));
    }

    public function delete(Request $request, $id)
    {
        if(Staff::find($id))
        {
            $staff = Staff::find($id);
            User::where(['id'=>$staff->user_id])->delete();
            $staff->delete();
            return redirect()->back()->with('success', 'Staff deleted successfully');
        }else{
            return redirect()->back()->with('error', 'Faield');
        }
    }
}
