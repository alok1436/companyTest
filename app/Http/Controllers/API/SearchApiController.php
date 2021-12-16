<?php
namespace App\Http\Controllers\API;
use Cart;
use Session;
use App\Role;
use App\User;
use App\Order;
use Validator;
use App\Company;
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

class SearchApiController extends Controller
{
    
    /**
     * Search products
     * @Role: API
     * @param  \Illuminate\Http\Request  $request
     * @return JSON
     */
    public function search(Request $request)
    {

    	 $validator = Validator::make($request->all(), [
                'keyword' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json(['error'=>$validator->errors()], 401);            
            } 

            $collection = Product::select('id','name','slug','product_type');
            
            //$collection->where(1,1);

        	$collection->where('products.name', 'like', '%'.request()->get('keyword'). '%');

            $collection->orwhereHas('categories', function($query){
                 $query->where('name', 'like', '%'. request()->get('keyword'). '%');
            });

            $collection->orwhereHas('brands', function($query){
                 $query->where('name', 'like', '%'. request()->get('keyword'). '%');
            });   
          
            $collection->orderBy('name','asc');

            $collection->where('status','Enable');

            $product = $collection->orderBy('id','desc')->get();
        	return response()->json([
                    'success' => 200,
                    'results'    => $product,
                    'message' =>'Product loaded.'
                ]);
    }
}