<?php

namespace App\Http\Controllers\Admin;
use File;
use App\User;
use App\Models\NewClass;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class StudentController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */

    public const REDIRECT_URL = 'admin/students';

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
        $students = User::whereHas('roles', function($q){
                         $q->where('name', 'student');
                    })->get();
        return view('admin.students.index', compact('students'));
    }

    public function store(Request $request)
    {
        //Create Banner
        if( $request->isMethod('post') && $request->ajax()){

            $validator = Validator::make($request->all(), [
                'first_name'    => 'required',
                'last_name'     => 'required',
                'email'         => 'required',
                'password'      => 'required',
                'phone'         => 'required',
                'class_id'      => 'required',
                'user_profile'  => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json(['error'=>$validator->errors()], 401);            
            } 
            //end Validation

            $student = new User();
            foreach($request->all() as $key=>$value){
                if( in_array( $key,$student->getFillable() ) ){
                    $student->$key = $value;
                }
            }   

            if($request->hasFile('user_profile'))
            {
                $image = $request->file('user_profile');
                $file_name = 'image_'.time().'.'.$image->getClientOriginalExtension();
                $destinationPath = public_path('images/banners');
                if (! File::exists($destinationPath)){
                    File::makeDirectory( $destinationPath );
                }
                $student->user_profile = 'images/students/'.$file_name;
                $image->move( $destinationPath, $file_name );
            }             
            
            $banner->save();

            $banner->categories()->attach($category);
            return response()->json([
                    'success' => true,
                    'data'   => [],
                    'message'   =>'Banner has been created.',
                    'redirect_url'   =>url(self::REDIRECT_URL),
                    'reload'=>false
            ]);
        }
        $classes = NewClass::pluck('name','id');
        $classes->prepend('Select Class','0');
        return view('admin.students.store',compact('classes'));
    }
    public function edit(Request $request, $id)
    {
        //Update banner
        $banner = Banner::find($id);
        if( $request->isMethod('post') && $request->ajax()){

            $validator = Validator::make($request->all(), [
                'title' => 'required',
                'sub_title' => 'required',
                'slug' => 'required'
            ]);
            if ($validator->fails()) {
                return response()->json(['error'=>$validator->errors()], 401);            
            } 
            //end Validation
            foreach($request->all() as $key=>$value){
                if( in_array( $key,$banner->getFillable() ) ){
                    $banner->$key = $value;
                }
            }   

            if($request->hasFile('image'))
            {
                $image = $request->file('image');
                $file_name = 'image_'.time().'.'.$image->getClientOriginalExtension();
                $destinationPath = public_path('images/banners');
                if (! File::exists($destinationPath)){
                    File::makeDirectory( $destinationPath );
                }
                $banner->image = 'images/banners/'.$file_name;
                $image->move( $destinationPath, $file_name );
            }    
            if($request->hasFile('video'))
            {
                $video = $request->file('video');
                $file_name = 'video_'.time().'.'.$video->getClientOriginalExtension();
                $destinationPath = public_path('images/banners');
                if (! File::exists($destinationPath)){
                    File::makeDirectory( $destinationPath );
                }
                $banner->video = 'images/banners/'.$file_name;
                $video->move( $destinationPath, $file_name );
            }         
            $banner->save();
            $category = [$request->category_level_1,$request->category_level_2,$request->category_level_3];
            $banner->brands()->sync($request->brand_id);
            $banner->categories()->sync($category);

            return response()->json([
                    'success' => true,
                    'data'   => [],
                    'message'   =>'Banner has been updated.',
                    'redirect_url'   =>url(self::REDIRECT_URL),
                    'reload'=>false
            ]);
        }

        $category = Category::where('parent_id',0)->where('status','Enable')->pluck('name','id');
        $category->prepend('Select Category','0');
        $brands = Brand::orderBy('name','ASC')->pluck('name','id');
        return view('admin.banners.edit',compact('brands','category','banner'));
    }

    public function delete(Request $request, $id)
    {
        if (Banner::find($id)) 
        {
            Banner::find($id)->delete();
            return redirect()->back()->with('success', 'Banner deleted successfully');
        }else{
            return redirect()->back()->with('error', 'Faield');
        }
    }

    public function removeVideo(Request $request, $id)
    {
        if (Banner::find($id)) 
        {
            $banner = Banner::find($id);
            $banner->video = '';
            $banner->save();
            return redirect()->back()->with('success', 'Video removed successfully');
        }else{
            return redirect()->back()->with('error', 'Faield');
        }
    } 
}
