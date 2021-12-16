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
use Illuminate\Support\Facades\Password;

class ReviewApiController extends Controller
{
    /**
     * 
     * @Role: API
     * @param  \Illuminate\Http\Request  $request
     * @return JSON
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
                'order_id' => 'required',
                'product_id' => 'required',
                'title' => 'required',
                'description' => 'required',
                'rating'=>'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['error'=>$validator->errors()], 401);            
        }
        $order = Order::find($request->get('order_id'));
 
        if(!empty($order) && $order->status == 'Completed'){

            if(auth('api')->user()->id == $order->customer->id){

                if($order->item->whereId($request->get('product_id'))->count() > 0){
                    
                    if($order->item->productReview->where('customer_id',$order->customer->id)->count()  == 0){

                            $productReview = new ProductReview();
                            foreach($request->all() as $key=>$value){
                                if( in_array( $key,$productReview->getFillable() ) ){
                                    $productReview->$key = $value;
                                }
                            }
                            $productReview->customer_id = auth('api')->user()->id;
                            $productReview->save();

                            return response()->json([
                                'success' => 200,
                                'results'=> $productReview,
                                'message' =>'Thank you for your review.'
                            ],200);
                        }else{
                            return response()->json([
                                'success' => 409,
                                'message' =>'You have already posted the review.'
                            ],409);
                        }
                    }else{
                       return response()->json([
                            'success' => 400,
                            'message' =>'This order doesn\'t having this product, please try again'
                        ],400);
                    }
                }else{
                return response()->json([
                    'success' => 400,
                    'message' =>'Invalid customer request, only that customer can post review who bought this product, thanks.'
                ],400); 
            }
        }else{
            return response()->json([
                'success' => 400,
                'message' =>'This order hasn\'t. completed status so before publish the review you will be waiting for completing this order, thank you.'
            ],400);  
        }
    }
}
