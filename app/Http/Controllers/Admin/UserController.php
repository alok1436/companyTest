<?php

namespace App\Http\Controllers\Admin;

use App\User;
use App\Role;
use App\Feedback;
use App\UserRole;
use App\RoleRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */

    public const REDIRECT_URL = 'admin/users';

    public function __construct()
    {
        
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request)
    {
        $users = User::orderBy('id','desc')->get();
        return view('admin.users.index', compact('users'));
    }


    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function role_requests(Request $request)
    {
        // $users = User::paginate(5);
        $data = RoleRequest::orderBy('id','desc')->get();
         return view('admin.users.role_requests', compact('data'));
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function admin_role_request_change_status(Request $request,$id)
    {
        $roleRequest = RoleRequest::find($id);
        if( $request->isMethod('post') && $request->ajax()){

            $validator = Validator::make($request->all(), [
                'status' => 'required'
            ]);
            if ($validator->fails()) {
                return response()->json(['error'=>$validator->errors()], 401);            
            } 
            //end Validation
            
            $user = $roleRequest->user;
            $user->new_role_request = 0;
            $user->approved_status = 'Approved';
            $user->save();

            $roleRequest->status = 'Approved';
            $roleRequest->save();

            $user->setMeta('new_role_request', 1);
            $user->setMeta('approved_status','Unapproved');

            return response()->json([
                    'success' => true,
                    'data'   => [],
                    'message'=>'Status has been Changed.',
                    'redirect_url'   =>url(self::REDIRECT_URL),
                    'reload' => false
            ]);
        }
    }

    public function store(Request $request)
    {
        //Create User
        if( $request->isMethod('post') && $request->ajax()){

            //Start Validation
            $messages = [
              'phone.required' => 'Phone field is required.',
              'password.required' => 'Password field is required.',
              'email.required' => 'Email field is required.',
              'first_name.required' => 'First name field is required.',
            ];
            $validator = Validator::make($request->all(), [
                'password' => 'required|min:6',
                'email' => 'required|email|unique:users',
                'phone' => 'required|unique:users|digits:10',
                'first_name' => 'required|alpha|min:2|max:255',
                'last_name' => 'required|alpha|min:2|max:255',
            ],$messages);
            if ($validator->fails()) {
                return response()->json(['error'=>$validator->errors()], 401);            
            } 
            //end Validation

            $user = new User();
            foreach($request->all() as $key=>$value){
                if( in_array( $key,$user->getFillable() ) ){
                    $user->$key = $value;
                }
            }   
            $user->password = Hash::make ( $request->get ('password') ); 
            $user->setMeta('from', 'Admin');   
            $user->save();
            $user
            ->roles()
            ->attach($request->role_name);

            return response()->json([
                    'success' => true,
                    'data'   => [],
                    'message'   =>'Account has been created.',
                    'redirect_url'   =>url(self::REDIRECT_URL),
                    'reload' => false
            ]);
        }
        $roles = Role::pluck('description','id');
        return view('admin.users.store',compact('roles'));
    }
    public function edit(Request $request, $id)
    {
        //Update User
        $user = User::find($id);
        $roleUser = UserRole::where('user_id',$id)->get()->first();
        if( $request->isMethod('post') && $request->ajax()){

            //Start Validation
            $messages = [
              'phone.required' => 'Phone field is required.',
              'email.required' => 'Email field is required.',
              'first_name.required' => 'First name field is required.',
              
            ];
            $validator = Validator::make($request->all(), [
                'phone' => 'required|unique:users,phone,'.$user->id,
                'email' => 'required|unique:users,email,'.$user->id,
                'first_name' => 'required',
            ],$messages);
            if ($validator->fails()) {
                return response()->json(['error'=>$validator->errors()], 401);            
            } 
            //end Validation

            foreach($request->all() as $key=>$value){
                if( in_array( $key,$user->getFillable() ) ){
                    $user->$key = $value;
                }
            } 
            if(trim($request->get ('password')) !=''){
                $user->password = Hash::make ( $request->get ('password') );
            }  
            $user->save();
            $user->roles()->sync($request->role_name);

            return response()->json([
                    'success' => true,
                    'data'   => [],
                    'message'   =>'Account has been updated.',
                    'redirect_url'   =>url('admin/user/edit/'.$user->id)
            ]);
        }
        $roles = Role::pluck('description','id');
        return view('admin.users.edit', compact('user','roles','roleUser'));
    }

    public function delete(Request $request, $id)
    {
        if (User::find($id)) 
        {
            $user = User::find($id);
            UserRole::where('user_id',$id)->delete();
            $user->delete();
            return redirect()->back()->with('success', 'Account deleted successfully');
        }else{
            return redirect()->back()->with('error', 'Faield');
        }
    } 


    // manage vendor feedback 

    public function manage_feedback(){
        $feedbacks = Feedback::orderBy('id', 'DESC')->get();
        return view('admin.feedbacks.feedback',compact('feedbacks'));
    }

     public function feedback_delete(Request $request, $id)
        {
            if (Feedback::find($id)) 
            {
                $user = Feedback::find($id);
                $user->delete();
                return redirect()->back()->with('success', 'Feedback deleted successfully');
            }else{
                return redirect()->back()->with('error', 'Faield');
            }
        }
}
