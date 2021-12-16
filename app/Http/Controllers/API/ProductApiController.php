<?php
namespace App\Http\Controllers\API;
use Session;
use App\Role;
use App\User;
use App\Order;
use Validator;
use App\Company;
use App\Product;
use App\ProductReview;
use Illuminate\Support\Str;
use Illuminate\Http\Request; 
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth; 
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use App\Mail\Vendor\VendorRegistered;
use App\Mail\Vendor\VendorVerification;
use App\Http\Resources\ProductCollection;
use Illuminate\Support\Facades\Password;

class ProductApiController extends Controller
{
    /**
     * Get products for home page
     * @Role: API
     * @param  \Illuminate\Http\Request  $request
     * @return JSON
     */
    public function get_products_for_home_page(Request $request)
    {
        $products = Product::select('id','name','slug','product_type')->where('status','Enable')->orderBy(DB::raw('RAND()'))->orderBy('id','desc')->paginate(20);

        return response()->json([
            'success' => true,
            'results'    => $products,
            'message' =>'Product loaded.'
        ]);
    }

/**
     * Get products for home page
     * @Role: API
     * @param  \Illuminate\Http\Request  $request
     * @return JSON
     */
    public function get_recent_products(Request $request)
    {
        $products = Product::select('id','name','slug','product_type')->where('status','Enable')->orderBy(DB::raw('RAND()'))->orderBy('id','desc')->paginate(30);
         
        return response()->json([
            'success' => true,
            'results'    => $products,
            'message' =>'Product loaded.'
        ]);
    }
    /**
     * Get products
     * @Role: API
     * @param  \Illuminate\Http\Request  $request
     * @return JSON
     */
    public function get_products(Request $request)
    {
        $collection = Product::where('status','Enable');

        if($request->filled('type') && $request->get('type')== 'featured'){
            $collection->where(['featured'=>1]);
            if(get_setting('fet_pro_suffle') == 'Yes'){
                $collection->orderBy(DB::raw('RAND()')); //suffle the data if yes
            }else{
                $collection->orderBy('id','desc');
            }
            $products = $collection->paginate((trim(get_setting('fet_pro_records')) !='') ? get_setting('fet_pro_records') : 20); 
        }else{
            $products = $collection->orderBy('id','desc')->paginate(20); 
        }
        return response()->json([
            'success' => true,
            'results'    => $products,
            'message' =>'Product loaded.'
        ]);
    }

    /**
     * Get products
     * @Role: API
     * @param  \Illuminate\Http\Request  $request
     * @return JSON
     */
    public function top_products(Request $request)
    {   
        $products = Product::withCount('orders')
        ->orderBy('orders_count','desc')
        ->orderBy('visitors','desc')
        ->where('status','Enable')
        ->paginate(10);
 
        return response()->json([
            'success' => true,
            'results'    => $products,
            'message' =>'Product loaded.'
        ]);
    }    

    /**
     * Single product get

     * params { id, slug }
     * @Role: API
     * @param  \Illuminate\Http\Request  $request
     * @return JSON
     */
    public function get(Request $request)
    {
        if($request->params){
           $clauseType = is_numeric($request->params) ? 'whereId' : 'whereSlug' ;
           $product = Product::with('author','categories','productReview')->$clauseType($request->params)->where('status','Enable')->get()->first();
           
           if($product){
                $this->product_per_visit_count($product);
                return response()->json([
                    'success' => 200,
                    'results'    => $product,
                    'message' =>'Product loaded.'
                ]); 
            }

            return response()->json([
                'success' => 400,
                'message' =>'No product found.'
            ]); 
        }
    }

    public function product_per_visit_count(Product $product){
        $visitors = $product->visitors >0 ? $product->visitors+1 : 1;
        $product->visitors = $visitors;
        $product->save();
    }

    /**
     * Single product get by vendor id

     * params { id}
     * @Role: API
     * @param  \Illuminate\Http\Request  $request
     * @return JSON
     */
    public function get_product_by_vendor(Request $request,$id)
    {
        if($id){
           $product = Product::with('author','categories')->where('vendor_id',$id)->where('status','Enable')->orderBy('id','desc')->get();
           if($product){
               return response()->json([
                    'success' => 200,
                    'results'    => $product,
                    'message' =>'Product loaded.'
                ]); 
            }

            return response()->json([
                'success' => 400,
                'message' =>'No product found.'
            ]); 
        }
    }
    /**
     * product get by category
     * @Role: API
     * @param  \Illuminate\Http\Request  $request
     * @return JSON
     */
    public function getproductByCategoryId(Request $request)
    {
        return new ProductCollection(Product::select('id','name')->paginate());
    }

   /**
     * product review get
     * @Role: API
     * @param  \Illuminate\Http\Request  $request
     * @return JSON
     */

    public function product_review_get(Request $request)
    {
           $validator = Validator::make($request->all(), [
                'product_id' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['error'=>$validator->errors()], 401);            
        }
        $product = Product::find(request()->product_id);
   
       if($product){
                foreach ($product->ProductReview as $key => $value) {
                      $data['review'][] = [
                                    'name'=>$value->customer->first_name.' '. $value->customer->last_name,
                                    'email'=>$value->customer->email,
                                    'title'=>$value->title,
                                    'description'=>$value->description
                                    ];

                }
               return response()->json([
                    'success' => 200,
                    'results'    => $data,
                    'message' =>'Review loaded.'
                ]); 
            }else{
               return response()->json([
                    'success' => 400,
                    'results'    => [],
                    'message' =>'Review loaded.'
                ]);  
            }
    }


       /**
     * product review store
     * @Role: API
     * @param  \Illuminate\Http\Request  $request
     * @return JSON
     */

    public function product_review_store(Request $request)
    {
        $validator = Validator::make($request->all(), [
                'customer_id' => 'required',
                'product_review' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['error'=>$validator->errors()], 401);            
        }
        $user = User::find($request->get('customer_id'));
        if(!empty($user)){
            foreach ($request->get('product_review') as $key => $product_review) {
                 $product = Product::find($product_review['id']);
              if($product){
                    $reviewData = new ProductReview;
                    $reviewData->product_id = $product->id;
                    $reviewData->customer_id = $user->id;
                    $reviewData->title      = $product_review['title'];
                    $reviewData->description = $product_review['description'];
                    $reviewData->save();
              }
            }
          return response()->json([
                'success' => 200,
                'results'    => $reviewData,
                'message' =>'review submited success.'
            ]);
                      

        }else{
           return response()->json([
                'success' => 400,
                'message' =>'Invalid user!.'
            ]);  
        }
    }
}
