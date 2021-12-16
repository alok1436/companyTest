<?php
namespace App\Http\Controllers;

use Auth;
use File;
use App\User;
use App\Brand;
use App\Offer;
use App\Banner;
use App\Product;
use App\Category;
use App\SubCategory;
use App\ProductCategory;
use App\ProductSubCategory;
use App\FirebaseNotification;
use App\ProductsImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use Intervention\Image\Facades\Image as Image;
use Illuminate\Support\Facades\Validator;

class CommonController extends Controller
{   
    public function saveToken(Request $request){
        $user = Auth::User();
        $user->device_token = $request->token;
        $user->save();

    }
    public function generateHtmlPartVariations(Request $request){
        $variation_option = $request->variation_option;
        $html = view('common.products.generate_variations',compact('variation_option'))->render();
        return response()->json(['success'=>true,'html'=>$html]);
    }

    public function getSubcategories(Request $request,$id){
        if ($request->ajax()) { 
            $html = '';
            $category = Category::find( $id );
            if($request->type == 'offer'){
                $product = $request->product_id >0 ? Offer::find( $request->product_id )->categories->pluck('id')->toArray() : [];
            }else if($request->type == 'firebaseNotification'){
                $product = $request->product_id >0 ? FirebaseNotification::find( $request->product_id )->categories->pluck('id')->toArray() : [];
            }else if($request->type == 'banner'){
                $product = $request->product_id >0 ? Banner::find( $request->product_id )->categories->pluck('id')->toArray() : [];
            }else{
                $product = $request->product_id >0 ? Product::find( $request->product_id )->categoriesTemp->pluck('id')->toArray() : [];
            }
            
            if($category){
                $subCategories = $category->children->where('status','Enable')->pluck('name','id');
                $subCategories->prepend('Select Category','0');
                foreach ($subCategories as $id => $value) {
                    $selected = in_array($id, $product) ? 'selected' : '';
                    $html .= '<option value="'.$id.'" '.$selected.'>'.$value.'</option>';
                }
            }
            return response()->json(['success'=>true,'html'=>$html]);
        }
    }
    /**
     * Method to save gallery image
     * save image on drag & drop
     * @return success or fail message
     */
    public function saveImages1(Request $request)
    {   
        $destinationPath = public_path('/images/products/gallery');
        if (! File::exists( $destinationPath ) ) {
            File::makeDirectory( $destinationPath );
        }
        if ($request->hasFile('file')) {
            $file = $request->file('file'); dd($file);
            $number_of_files_uploaded = count($file);
            if ($number_of_files_uploaded > 10){ 
            // checking how many images your user/client can upload
                return response()->json([
                        'success' => false,
                        'message' => "You can upload 10 Images."
                ]); 
            }else{  
                for ($i = 0; $i < $number_of_files_uploaded; $i++) {
                    $filename = time().uniqid(). '.' . $request['file'][$i]->extension();
                    $file[$i]->move( $destinationPath, $filename );
                    $totalImages[] = array('fileName'=>'images/products/gallery/'.$filename);
                }
                return response()->json($totalImages); 
            }
        } 
    }

    public function saveImages(Request $request){

       
        $rules = array(
            'file' => 'required|image|mimes:jpeg,png,jpg,gif|max:5000',
            'dimensions:min_height=1667,min_width=2500'
        );

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(['error'=>$validator->errors()], 401);            
        }

        $file =  $request->file('file');


        $file1 = $file;
        $destinationPath = public_path('images/products/gallery');
        $destinationPath1 = public_path('images/products/gallery/');
        if (! File::exists( $destinationPath ) ) {
            File::makeDirectory( $destinationPath );
        }

        $extension = $file->extension();
        $files1 = sha1(time().time());
        $filename = $files1.".{$extension}";

        $upload_success = $file->move( $destinationPath, $filename );
 
        if( $upload_success ) {
            $uploadPath = $destinationPath.'/'.$filename;
            if(file_exists($uploadPath)){
                $img = Image::make($uploadPath);
                // side will be scaled to maintain the original aspect ratio
                $img->resize(1200, null, function ($constraint) {
                    $constraint->aspectRatio();
                // $constraint->upsize();
                });
                $img->encode($extension);
                $img->save($destinationPath. '/' . $filename,100);
            }            
            $image = array('fileName'=>'images/products/gallery/'.$filename);
            return response()->json(['success'=>true,'image'=>$image, 200]);
        } else {
            return response()->json(['error', 400]);
        }
    }
   
    /**
     * Method to delete gallery image
     * @id is pk of product
     * @img_name is the name of image
     * @return success or fail message
     */
    public function deleteGalleryImage(Request $request)
    {   
        $id  = $request->get('id');
        $product_image = ProductsImage::find($id);
        $image_path = $product_image->image;
        if(File::exists($image_path)) {
            File::delete($image_path);
        }
        ProductsImage::where('id',$id)->delete();
        
        return response()->json([
                    'success' => true,
                    'message'   =>'Success, Image Deleted'
            ]);
    
    }

    public function test(){

        //$dir = public_path('images/products/gallery');

        // Sort in ascending order - this is default
        //$a = scandir($dir);
        //unset($a[0]);
        //unset($a[1]);
        // foreach ($a as $key => $value) {
            $image_name = 'nature.jpg';
            $new_width =    1200;
            $new_height = 1200;
            $uploadDir =  public_path('images/products/abc');
            $moveToDir =   public_path('images/products/gallery2/');
            createThumbnail($image_name,$new_width,$new_height,$uploadDir,$moveToDir);
        // }
    }
}