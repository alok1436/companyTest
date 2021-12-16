<?php
namespace App\Http\Controllers\API;
  
use Session;
use App\Role;
use App\User;
use Validator;
use App\Order;
use App\Company;
use App\Enquiry;
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

class DeliveryGuysApiController extends Controller
{
    /**
     * get order
     * @Role: API
     * @param  \Illuminate\Http\Request  $request
     * @return JSON
     */
    public function get(Request $request){
        $order = auth('api')->user()->deliveryGuyOrders()->with('mainOrder')->where('sub_order_code',$request->subordercode)->first();
        return response()->json([
            'success' => true,
            'results'    => $order,
            'message' =>'Order loaded.'
        ]);
    }
    /**
     * Dispatch orders
     * @Role: API
     * @param  \Illuminate\Http\Request  $request
     * @return JSON
     */
    public function orders(Request $request){

        if( $request->isMethod('post')){
            $validator = Validator::make($request->all(), [
                'status'=> 'required'
            ]); 
            if ($validator->fails()) {
                return response()->json(['error'=>$validator->errors()], 400);            
            }    

           $collection = auth('api')->user()->deliveryGuyOrders();

            if(isset($request->from_date) && $request->from_date !='' && isset($request->to_date) && $request->to_date !=''){
            $collection->whereBetween('orders.created_at', [$request->from_date." 00:00:00", $request->to_date." 23:59:59"]);

            }else if(isset($request->from_date) && $request->from_date !=''){
                $collection->whereDate('orders.created_at',$request->from_date);

            }else if(isset($request->to_date) && $request->to_date !=''){
                $collection->whereDate('orders.created_at',$request->to_date);
            }


            if(request()->brand >0){
                $collection->whereHas('item.brands', function($q){
                    $q->whereBrandId(request()->brand);  
                });
            }

            if($request->sub_order_code !=''){
                $collection->where('sub_order_code',$request->sub_order_code)->orwhere('order_code',$request->sub_order_code);
            }           

            if($request->get('status')){
                $collection->where('status',$request->get('status'));
            }

            $orders = $collection->orderBy('id','DESC')->get();

            return response()->json([
                'success' => true,
                'results'    => $orders,
                'message' =>'Orders loaded.'
            ]);
        }
    }

    /**
     * Get all orders
     * @Role: API
     * @param  \Illuminate\Http\Request  $request
     * @return JSON
     */
    public function update_status(Request $request)
    {
         if( $request->isMethod('post')){
            $validator = Validator::make($request->all(), [
                'id' => 'required|exists:App\Order,id',
                'status'=> 'required',
                'reason' => $request->get('status') == 'Cancelled' ? 'required|min:10' :'',
            ]);

            if ($validator->fails()) {
                return response()->json(['error'=>$validator->errors()], 401);            
            }

            $order = auth('api')->user()->deliveryGuyOrders()->wherePivot('order_id', $request->get('id'))->first();
            if($order){
                $order->status = $request->get('status');
                $order->save();

                if($request->get('status') == 'Cancelled'){
                    $order->setMeta('reason',$request->get('reason'));
                    $order->save();
                }
                return response()->json([
                    'success' => 200,
                    'results'    => [],
                    'message' => 'Thank you, order status has been changed.'
                ]);
            }else{
                return response()->json([
                    'success' => 400,
                    'message' =>'Invalid order request, only that guys can take action who get assigned this order, thanks.'
                ]); 
            }
        }       
    }
    
    public function analytics(Request $request){
            $user = auth('api')->user();
            if($user->roles()->first()->name == 'delivery'){
                $results['total_order_count'] = $user->deliveryGuyOrders->count();
                $results['total_sale_completed'] = $user->deliveryGuyOrders->where('status','Completed')->sum('item_total');
                $results['total_sale_dispatched'] = $user->deliveryGuyOrders->where('status','Dispatch')->sum('item_total');
                $results['total_sale_cancelled'] = $user->deliveryGuyOrders->where('status','Cancelled')->sum('item_total');
                $results['latest_order'] =  $user->deliveryGuyOrders;
                return response()->json([
                'success' => 200,
                'data'    => $results,
                'message' =>'Enquiry has been send successfull!.'
             ]);

            }else{
                return response()->json([
                    'success' => 400,
                    'message' =>'Only vendors can access'
                ],400);
            }
    }
}