<?php

namespace App\Http\Controllers\admin;

use Hash;
use File;
use App\Role;
use App\Models\NewClass;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class ClassController extends Controller
{
    public const REDIRECT_URL = 'admin/classes';

    public function index(Request $request)
    {
        $classes = NewClass::get();
        return view('admin.classes.index',compact('classes'));
    }

    public function store(Request $request)
    {
        //Create and Update Specialization
        if( $request->isMethod('post') && $request->ajax()){

            //Start Validation
            $messages = [
              'name.required' => 'First name field is required.',
            ];
            $validator = Validator::make($request->all(), [
                'name'    => 'required',
            ],$messages);
            if ($validator->fails()) {
                return response()->json(['error'=>$validator->errors()], 401);            
            } 
            //end Validation

            $class = new NewClass();
            foreach($request->all() as $key=>$value){
                if( in_array( $key,$class->getFillable() ) ){
                    $class->$key = $value;
                }
            }  
            $class->save();

            return response()->json([
                    'success' => true,
                    'data'   => [],
                    'message'=> 'Class has been created.',
                    'redirect_url'   =>url(self::REDIRECT_URL)
            ]);
        }
        return view('admin.classes.store');
    }

    public function edit(Request $request,$id)
    {
        //Create and Update Specialization
        $class = NewClass::find($id);
        if( $request->isMethod('post') && $request->ajax()){

            //Start Validation
            $messages = [
              'name.required' => 'First name field is required.',
            ];
            $validator = Validator::make($request->all(), [
                'name'    => 'required',
            ],$messages);
            if ($validator->fails()) {
                return response()->json(['error'=>$validator->errors()], 401);            
            } 
            //end Validation

             
            foreach($request->all() as $key=>$value){
                if( in_array( $key,$class->getFillable() ) ){
                    $class->$key = $value;
                }
            }  
            $class->save();
            
            return response()->json([
                'success'   => true,
                'data'      => [],
                'message'   => 'Class has been updated.',
                'redirect_url' => url(self::REDIRECT_URL)
            ]);
        }
        return view('admin.classes.edit',compact('class'));
    }

    public function delete(Request $request, $id)
    {
        if(NewClass::find($id))
        {
            NewClass::find($id)->delete();
            return redirect()->back()->with('success', 'Class has been deleted successfully');
        }else{
            return redirect()->back()->with('error', 'Faield');
        }
    }
}
