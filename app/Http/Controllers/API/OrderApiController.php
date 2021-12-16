<?php
namespace App\Http\Controllers\API;
  
use Session;
use App\Role;
use App\User;
use App\Order;
use App\MainOrder;
use Validator;
use App\Product;
use App\Services\OrderService;
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

class OrderApiController extends Controller
{

    public $errors = [];
    public $service;

    public function __construct(OrderService $OrderService){
        $this->service =  $OrderService;
    }
    /**
     * Create new order
     * @Role: API
     * @param  \Illuminate\Http\Request  $request
     * @return JSON
     */
    public function create(Request $request)
    {
        if( $request->isMethod('post')){

            $validator = Validator::make($request->all(), [
                'payment_method' => 'required',
                'payment_status' => 'required',
                "billing_address"=> 'required',
                "shipping_address"=> 'required'
            ]);
            if ($validator->fails()) {
                return response()->json(['error'=>$validator->errors()], 401);            
            }

            $data = $this->service->createOrder($request);

            if(empty($data['errors'])){
                return response()->json([
                    'success' => 200,
                    'results'    => $data,
                    'message' => empty($data['errors']) ? 'Order has been placed.' : ''
                ]);                
            }else{
                return response()->json([
                    'success' => false,
                    'message' =>$data['errors']
                ],401);               
            }
        }
        return response()->json([
            'success' => false,
            'message' =>'Bad request'
        ],401);
    }

    /**
     * Vendor change order status
     * @Role: API
     * @param  \Illuminate\Http\Request  $request
     * @return JSON
     */
    public function changeOrderStatus(Request $request, $id)
    {
        $order = Order::find($id);
        if( $request->isMethod('post')){
            $order->status = $request->get('status');
            $order->save();
            return response()->json([
                'success' => true,
                'message' =>'Order status has been changed successfull.'
            ]);
        }
        return response()->json([
            'success' => false,
            'message' =>'Bad request'
        ],401);
    }


    /**
     * Get all customers orders
     * @Role: API
     * @param  \Illuminate\Http\Request  $request
     * @return JSON
     */
    public function get(Request $request)
    {
        /*$array = array();
        $orders = Order::where('customer_id',auth('api')->user()->id)->orderBy('id','desc')->groupBy('order_code')->get();
        if(!empty($orders)){
            foreach ($orders as $key => $order) {
                $meta = $order->getMeta();
                $array[$key]['order_code'] = $order->order_code;
                $array[$key]['payment_method'] = $order->payment_method;
                $array[$key]['payment_status'] = $order->payment_status;
                $array[$key]['subtotal'] = $order->subtotal;
                $array[$key]['discount'] = $order->discount;
                $array[$key]['shipping_charge'] = $order->shipping_charge;
                $array[$key]['total'] = $order->total;
                $array[$key]['items'] = Order::with('item')->where(['customer_id'=>auth('api')->user()->id,'order_code'=>$order->order_code])->get();
            }
        }
*/
        $orders = auth('api')->user()->mainOrders()->with('suborders')->orderBy('id','DESC')->get();
        return response()->json([
            'success' => 200,
            'results' => $orders
        ],200);
    }

    /**
     * Cancel the order
     * @Role: API
     * @param  \Illuminate\Http\Request  $request
     * @return JSON
     */
    public function cancel_order(Request $request)
    {
         if( $request->isMethod('post')){

            $validator = Validator::make($request->all(), [
                'id' => 'required|exists:App\Order,id',
                'reason' => 'required|min:10',
            ]);

            if ($validator->fails()) {
                return response()->json(['error'=>$validator->errors()], 401);            
            }

            $order = Order::find($request->get('id'));
            if(auth('api')->user()->id == $order->customer->id){

                $order->status = 'Hold';
                $order->save();

                $order->setMeta('reason',$request->get('reason'));
                $order->save();

                return response()->json([
                    'success' => 200,
                    'results'    => [],
                    'message' => 'Thank you, your order has been canceled.'
                ]);

            }else{
                return response()->json([
                    'success' => 400,
                    'message' =>'Invalid customer request, only that customer can cancel this order who bought this product, thanks.'
                ]); 
            }
        }       
    }


    /**
     * Update order stauts
     * @Role: API
     * @param  \Illuminate\Http\Request  $request
     * @return JSON
     */
    public function update_order_status(Request $request)
    {
         if( $request->isMethod('post')){

            $validator = Validator::make($request->all(), [
                'id' => 'required|exists:App\Order,id',
                'reason' => 'required|min:10',
                'status' => 'required'
            ]);

            if ($validator->fails()) {
                return response()->json(['error'=>$validator->errors()], 401);            
            }

            $order = Order::find($request->get('id'));
            if(auth('api')->user()->id == $order->customer->id){

                $order->status = $request->get('status');
                $order->save();
                $order->setMeta('reason',$request->get('reason'));
                $order->save();

                return response()->json([
                    'success' => 200,
                    'results'    => [],
                    'message' => 'Thank you, your order status has been changed.'
                ]);

            }else{
                return response()->json([
                    'success' => 400,
                    'message' =>'Invalid customer request, only that customer can change this order status who bought this product, thanks.'
                ]); 
            }
        }       
    }

    /**
    * Update order stauts
    * @Role: API
    * @param  \Illuminate\Http\Request  $request
    * @return JSON
    */
    public function mainorder_update(Request $request)
    {
         if( $request->isMethod('post')){

            $validator = Validator::make($request->all(), [
                'order_code' => 'required',
                'payment_status' => 'required'
            ]);

            if ($validator->fails()) {
                return response()->json(['error'=>$validator->errors()], 401);            
            }

            $order = MainOrder::where('order_code',$request->get('order_code'))->get()->first();

            if($order){

            if(auth('api')->user()->id == $order->customer->id){

                    $order->payment_status = $request->payment_status;
                    $order->save();
                    
                    return response()->json([
                        'success' => 200,
                        'results'    => [],
                        'message' => 'Status has been updated.'
                    ]);

                }else{
                    return response()->json([
                        'success' => 401,
                        'message' =>'Invalid customer'
                    ]); 
                }

            }else{
                return response()->json([
                    'success' => 400,
                    'message' =>'Invalid order.'
                ]);    
            }
        }       
    }
}