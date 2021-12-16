<?php

namespace App\Http\Controllers\Teacher;

use Auth;
use App\User;
use App\Order;
use App\UserRole;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     'Pending','Completed','Processing','Cancelled','Refund','Accepted','Shipment','Ready for dispatch','Hold','Refunded','Dispatch','Return','Exchange'
     */
    public function index()
    {	
        
        $orders = Auth::user()->deliveryGuyOrders()->paginate(10);
        $CancelledOrders = Auth::user()->deliveryGuyOrders()->where('status','Cancelled')->count();
        $DispatchOrders = Auth::user()->deliveryGuyOrders()->where('status','Dispatch')->count();
        $CompletedOrders = Auth::user()->deliveryGuyOrders()->where('status','Completed')->count();
        $EarnedTotal = Auth::user()->deliveryGuyOrders()->where('status','Completed')->get()->sum('item_total');

        return view('delivery.dashboard.index',compact('orders','CancelledOrders','DispatchOrders','CompletedOrders','EarnedTotal'));
    }
}