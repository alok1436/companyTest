<?php

namespace App\Http\Controllers\Vendor;

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
use App\Exports\Vendor\OrderExport;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;

class OrderController extends Controller
{
    public const REDIRECT_URL = 'vendor/orders';

    public function index(Request $request)
    {
        $collection = Order::select('*');
        if(request()->status && request()->status =='all'){
            
        }else if(request()->status && request()->status !='Select status'){
             $collection->whereIn('status',[request()->status]);
        }else{
            $collection->whereIn('status',['Pending','Accepted','Ready for dispatch','Cancelled']);
        }
        

        if(isset($request->from_date) && $request->from_date !='' && isset($request->to_date) && $request->to_date !=''){
            $collection->whereBetween('created_at', [$request->from_date." 00:00:00", $request->to_date." 23:59:59"]);

        }else if(isset($request->from_date) && $request->from_date !=''){
            $collection->whereDate('created_at',$request->from_date);

        }else if(isset($request->to_date) && $request->to_date !=''){
            $collection->whereDate('created_at',$request->to_date);
        }
        
        if($request->sub_order_code){
            $collection->where('sub_order_code',$request->sub_order_code)->orwhere('order_code',$request->sub_order_code);
        }

        if(request()->brand >0){
            $collection->whereHas('item.brands', function($q){
                $q->whereBrandId(request()->brand);  
            });
        }

        $collection = $collection->where('vendor_id',Auth::User()->id)->orderBy('id','DESC')->groupBy(['order_code','vendor_id']);


        if($request->has('export')){
            $orders = $collection->orderBy('created_at','DESC')->get();
            return Excel::download(new OrderExport($orders), 'orders_.'.time().'.csv');
        }

        $orders = $collection->paginate(50);
        $orders->appends($request->all());


        $brand = Brand::orderBy('name','ASC')->get()->pluck('name','id');
        $brand->prepend('Select Brand','');
        return view('vendor.orders.index', compact('orders','brand'));
    }

    public function store(Request $request)
    {
        //Create Order
        if( $request->isMethod('post') && $request->ajax()){

            //Start Validation
            $messages = [
              'name.required' => 'Order name field is required.', 
            ];
            $validator = Validator::make($request->all(), [
                'name' => 'required',
                'price' => 'required',
                'shipping_charge' => 'required',
                'quantity' => 'required',
            ],$messages);
            if ($validator->fails()) {
                return response()->json(['error'=>$validator->errors()], 401);            
            } 
            //end Validation

            $order = new Order();
            foreach($request->all() as $key=>$value){
                if( in_array( $key,$order->getFillable() ) ){
                    $order->$key = $value;
                }
            }  
            $order->vendor_id = Auth::User()->id;
            $order->save();

            return response()->json([
                    'success' => true,
                    'data'   => [],
                    'message'=>'Order has been created.',
                    'redirect_url'   =>url(self::REDIRECT_URL)
            ]);
        }
        return view('vendor.orders.store');
    }

    public function edit(Request $request, $id)
    {
        //Edit and Update Order
        $order = Order::find($id);
        if( $request->isMethod('post') && $request->ajax()){

            //Start Validation
            $messages = [
              'name.required' => 'Order name field is required.', 
            ];
            $validator = Validator::make($request->all(), [
                'name' => 'required',
                'price' => 'required',
                'shipping_charge' => 'required',
                'quantity' => 'required',
            ],$messages);
            if ($validator->fails()) {
                return response()->json(['error'=>$validator->errors()], 401);            
            } 
            //end Validation

            foreach($request->all() as $key=>$value){
                if( in_array( $key,$order->getFillable() ) ){
                    $order->$key = $value;
                }
            } 
            $order->vendor_id = Auth::User()->id;
            $order->save();

            return response()->json([
                    'success' => true,
                    'data'   => [],
                    'message'=>'Order has been updated.',
                    'redirect_url'   =>url('vendor/order/edit/'.$order->id)
            ]);
        }
        
        return view('vendor.orders.edit',compact('order'));
    }

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

    public function changeStatus(Request $request)
    {

        if($request->ajax()){

            $validator = Validator::make($request->all(), [
                'id' => 'required|exists:App\Order,id',
                'status'=> 'required',
                'reason' => $request->get('status') == 'Cancelled' ? 'required|min:10' :'',
            ]);

            if ($validator->fails()) {
                return response()->json(['error'=>$validator->errors()], 401);            
            }
              $order_status = Order::find($request->id);
              $order_status->status = $request->status;
              $order_status->save();
              $order_status->setMeta('reason',$request->get('reason'));
              $order_status->save();
              if($order_status->save()){
                 return response()->json([
                    'success' => true,
                    'message'   =>'Status Change Successfully'
            ]);
              }else{
                return response()->json([
                    'success' => false,
                    'message'   =>'Success, try again'
            ],401);
              }

        }
    }

    public function vendorOrderChangeStatus(Request $request, $order_id)
    {

        if($request->ajax()){
            $vendorOrderStatus = Order::find($request->order_id);
            //dd($vendorOrderStatus->id);
            $vendorOrderStatus->status = $request->status;
            $vendorOrderStatus->save();
            if($vendorOrderStatus){
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

    public function order_view(Request $request, $vendor_id, $order_code){
        $order = Order::with('vendor')->where(['order_code'=>$order_code,'vendor_id'=>$vendor_id])->first();
        if(!$order) abort(404);
        if ($request->get('np_hash') > 0) {
            Notification::where('id',$request->get('np_hash'))->update(['read_at'=>date('Y-m-d H:i:s')]);
        }


        $users = User::whereHas('roles', function($q){
                    $q->where('name', 'delivery');
                })->get();
   
        $sub_orders =Order::with('vendor')->where(['order_code'=>$order_code,'vendor_id'=>$vendor_id])->get();
        return view('vendor.orders.view',compact('order','users','sub_orders'));
    }
}
