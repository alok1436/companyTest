<?php

namespace App\Http\Controllers\Student;

use Auth;
use App\User;
use App\Order;
use App\UserRole;
use App\Product;
use App\Variant;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    /**
    * Show the application dashboard.
    *
    * @return \Illuminate\Contracts\Support\Renderable
    */
    public function index()
    {	
        $products           = Product::with('author')->orderBy('stock', 'DESC')->where(['vendor_id'=>Auth::id()])->get();

        $lowStockProducts           = Product::with('author')->where('stock', '>', '3')->where(['vendor_id'=>Auth::id()])->get();
        $orderItem          = Order::with('item')->orderBy('item_id', 'DESC')->get();
        $orders             = Order::where(['vendor_id'=>Auth::id()])->get()->count();
        $latest_order       = Order::with('customer')->orderBy('id', 'DESC')->where(['status'=>'pending','vendor_id'=>Auth::id()])->paginate(10);
        $orderSale          = Order::where(['status'=>'Completed','vendor_id'=>Auth::id()])->get()->sum('item_total');
        $orderSalePending   = Order::where(['status'=>'pending','vendor_id'=>Auth::id()])->get()->sum('item_total');

        $PendingOrders      = Order::where(['status'=>'pending','vendor_id'=>Auth::id()])->count();

        $ProcessingOrders   = Order::where(['status'=>'Processing','vendor_id'=>Auth::id()])->count(); 

        $CancelOrders       = Order::where(['status'=>'Cancelled','vendor_id'=>Auth::id()])->count();
        $lowStockProductCount           = Product::with('author')->where('stock', '>', '3')->where(['vendor_id'=>Auth::id()])->count(); 
        
        //dd($PendingOrders);
        return view('vendor.dashboard.index',compact('orders','orderSale','latest_order','orderSalePending','products','lowStockProducts','PendingOrders','CancelOrders','ProcessingOrders','lowStockProductCount'));
    }
    public function menus()
    {   
        // dd(Auth::user());
        return view('vendor.wmenu.menu-html');
    }
}
