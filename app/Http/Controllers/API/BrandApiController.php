<?php
namespace App\Http\Controllers\API;
  
use Session;
use App\Role;
use App\User;
use App\Brand;
use App\Order;
use Validator;
use App\Company;
use App\Product;
use App\Variant;
use Illuminate\Support\Str;
use Illuminate\Http\Request; 
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth; 
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use App\Mail\Vendor\VendorRegistered;
use App\Mail\Vendor\VendorVerification;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Password;

class BrandApiController extends Controller
{
    /**
     * Get Brands
     * @Role: API
     * @param  \Illuminate\Http\Request  $request
     * @return JSON
     */
    public function get_brands(Request $request)
    {
        $brands = Brand::orderBy('position','ASC')->get();
         
        return response()->json([
            'success' => true,
            'results'    => $brands,
            'message' =>'Brands get successfull.'
        ]);
    }

    /**
     * Get Products
     * Params Tag {id, slug}
     * @Role: API
     * @param  \Illuminate\Http\Request  $request
     * @return JSON
     */
    public function get_products_by_brand(Request $request)
    {
        $slug = $request->params;
        $products = Product::with('brands')->whereHas('brands', function($query){
            $clauseType = is_numeric(request()->params) ? 'whereBrandId' : 'whereSlug' ; 
            $query->$clauseType(request()->params);
        })->where('status','enable')->orderBy('id','desc')->paginate(20);

        $clauseType = is_numeric(request()->params) ? 'whereId' : 'whereSlug' ;
         
        $brand = Brand::$clauseType($request->params)->first();
       
        if (!empty($products)) {
            return response()->json([
                'success' => true,
                'results'    => $products,
                'varients' => Variant::pluck('name','id'),
                'filters' => [
                    'categories'=>$brand ? $brand->categories : [] ,
                    'price'=>array(
                        'minimum'=>0,
                        'maxmium'=>10000,
                    ),
                ],
                'message' =>'Products loaded.'
            ]);
        }

        return response()->json([
                'success' => 400,
                'message' =>'No product found.'
        ]); 
    }
}