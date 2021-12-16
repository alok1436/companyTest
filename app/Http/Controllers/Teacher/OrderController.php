<?php

namespace App\Http\Controllers\Class;

use File;
use Auth;
use App\User;
use App\Role;
use App\Order;
use App\Staff;
use App\Brand;
use App\Category;
use App\SubCategory;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{
    public const REDIRECT_URL = 'delivery/orders';

    public function index(Request $request)
    {

        $collection = Auth::user()->deliveryGuyOrders();

        if(request()->status && request()->status =='all'){
            
        }else if(request()->status && request()->status !='Select status'){
             $collection->whereIn('status',[request()->status]);
        }else{
            $collection->whereIn('status',['Dispatch','Completed','Cancelled']);
        }
        
        if(isset($request->from_date) && $request->from_date !='' && isset($request->to_date) && $request->to_date !=''){
            $collection->whereBetween('orders.created_at', [$request->from_date." 00:00:00", $request->to_date." 23:59:59"]);

        }else if(isset($request->from_date) && $request->from_date !=''){
            $collection->whereDate('orders.created_at',$request->from_date);

        }else if(isset($request->to_date) && $request->to_date !=''){
            $collection->whereDate('orders.created_at',$request->to_date);
        }
        
        if($request->sub_order_code){
            $collection->where('sub_order_code',$request->sub_order_code)->orwhere('order_code',$request->sub_order_code);
        }
        $orders = $collection->orderBy('id','DESC')->paginate(50); 
        $orders->appends($request->all());

        return view('delivery.orders.index', compact('orders'));
    }

    // public function store(Request $request)
    // {
    //     //Create Order
    //     if( $request->isMethod('post') && $request->ajax()){

    //         //Start Validation
    //         $messages = [
    //           'name.required' => 'Order name field is required.', 
    //         ];
    //         $validator = Validator::make($request->all(), [
    //             'name' => 'required',
    //             'price' => 'required',
    //             'shipping_charge' => 'required',
    //             'quantity' => 'required',
    //         ],$messages);
    //         if ($validator->fails()) {
    //             return response()->json(['error'=>$validator->errors()], 401);            
    //         } 
    //         //end Validation

    //         $order = new Order();
    //         foreach($request->all() as $key=>$value){
    //             if( in_array( $key,$order->getFillable() ) ){
    //                 $order->$key = $value;
    //             }
    //         }  
    //         $order->vendor_id = Auth::User()->id;
    //         $order->save();

    //         return response()->json([
    //                 'success' => true,
    //                 'data'   => [],
    //                 'message'=>'Order has been created.',
    //                 'redirect_url'   =>url(self::REDIRECT_URL)
    //         ]);
    //     }
    //     return view('vendor.orders.store');
    // }

    // public function edit(Request $request, $id)
    // {
    //     //Edit and Update Order
    //     $order = Order::find($id);
    //     if( $request->isMethod('post') && $request->ajax()){

    //         //Start Validation
    //         $messages = [
    //           'name.required' => 'Order name field is required.', 
    //         ];
    //         $validator = Validator::make($request->all(), [
    //             'name' => 'required',
    //             'price' => 'required',
    //             'shipping_charge' => 'required',
    //             'quantity' => 'required',
    //         ],$messages);
    //         if ($validator->fails()) {
    //             return response()->json(['error'=>$validator->errors()], 401);            
    //         } 
    //         //end Validation

    //         foreach($request->all() as $key=>$value){
    //             if( in_array( $key,$order->getFillable() ) ){
    //                 $order->$key = $value;
    //             }
    //         } 
    //         $order->vendor_id = Auth::User()->id;
    //         $order->save();

    //         return response()->json([
    //                 'success' => true,
    //                 'data'   => [],
    //                 'message'=>'Order has been updated.',
    //                 'redirect_url'   =>url('vendor/order/edit/'.$order->id)
    //         ]);
    //     }
        
    //     return view('vendor.orders.edit',compact('order'));
    // }

    public function delete(Request $request, $id)
    {
        if(Order::find($id))
        {
            $order = Order::find($id);
            $order->delete();
            return redirect()->back()->with('success', 'Order deleted successfully');
        }else{
            return redirect()->back()->with('error', 'Faield');
        }
    }

    // public function changeStatus(Request $request)
    // {

    //     if($request->ajax()){
    //           $order_status = Order::find($request->id);
    //           $order_status->status = $request->status;
    //           $order_status->save();
    //           if($order_status->save()){
    //              return response()->json([
    //                 'success' => true,
    //                 'message'   =>'Status Change Successfully'
    //         ]);
    //           }else{
    //             return response()->json([
    //                 'success' => false,
    //                 'message'   =>'Success, try again'
    //         ],401);
    //           }

    //     }
    // }

    public function deliveryOrderChangeStatus(Request $request, $order_id)
    {

        if($request->ajax()){
            $deliveryOrderStatus = Order::find($request->order_id);
            //dd($deliveryOrderStatus->id);
            $deliveryOrderStatus->status = $request->status;
            $deliveryOrderStatus->save();
            if($deliveryOrderStatus){
                return response()->json([
                    'success' => true,
                    'message'   =>'Order Status Change Successfully'
                ]);
            }else{
                return response()->json([
                    'success' => false,
                    'message'   =>'Faield, try again'
                ],401);
            }
        }
    }

    public function order_view(Request $request, $sub_order_code)
    {
        //View Orders
        $order = Order::where('sub_order_code',$sub_order_code)->first();

        if(!$order) abort(404);

        return view('delivery.orders.view',compact('order'));
    }

    /**
     * update stats
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

            $order = Auth::user()->deliveryGuyOrders()->wherePivot('order_id', $request->get('id'))->first();
            if($order){
                $order->status = $request->get('status');
                $order->save();

                //if($request->get('status') == 'Cancelled'){
                    $order->setMeta('reason',$request->get('reason'));
                    $order->save();
                //}
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
}
