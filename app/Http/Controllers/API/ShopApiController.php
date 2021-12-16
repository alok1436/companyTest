<?php
namespace App\Http\Controllers\API;
  
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
use App\Http\Resources\ProductCollection;
use Illuminate\Support\Facades\Password;

class ShopApiController extends Controller
{
    /**
     * Search products
     * @Role: API
     * @param  \Illuminate\Http\Request  $request
     * @return JSON
     */
    public function index1(Request $request){
        echo "hello";
      //  dd(request()->get('category'));
        dd($request->all());
    }

    public function index(Request $request){
        $collection = Product::with('brands');

        if(request()->get('category')) {
            $collection->whereHas('categories', function($query){
                $query->whereIn('categories.id',request()->get('category'));
            });   
        }

        if(request()->get('sub_category')) {
            $collection->whereHas('categories', function($query){
                $query->whereIn('categories.id',request()->get('sub_category'));
            });   
        }

        if(request()->get('brand')) {
            $collection->whereHas('brands', function($query){
                $query->whereIn('brands.id',request()->get('brand'));
            });   
        }

        if(!empty(request()->get('tag'))) {
            $collection->whereHas('tags', function($query){
                $query->whereIn('tags.id',request()->get('tag'));
            });   
        }

        if(!empty(request()->get('variants'))) {
            $collection->join('sku_values','sku_values.product_id','=','products.id');
            $collection->whereIn('sku_values.variant_id',request()->get('variants'));
        }

        $collection->orderBy('id','desc');
        $collection->where('status','enable');

        if(request()->get('offers') == 1){
            $collection->whereHas('skus', function($query){
                $query->where('sale_price','!=',null);
            });
        }

        if(request()->get('offers') == 1){

            $collection->where('product_type','single');
            $products = $collection->get();       
            $products = $products->filter(function ($item) {
                if(request()->get('percentage') > 0){
                    return $item->getProductPriceDiffPercentage() == request()->get('percentage');
                }else{
                    return $item->getProductPriceDiffPercentage();
                }
            })->values();

        }else{
            $products = $collection->paginate(20); 
        }
        
        return response()->json([
            'success' => true,
            'results'    => $products,
            'message' =>'Products loaded.'
        ]); 
    }
}