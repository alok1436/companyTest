<?php

namespace App\Services;

use App\User;
use App\Sku;
use App\Order;
use App\MainOrder;
use App\Product;
use App\Affiliate;
use App\AffiliateOrder;
use App\StockHistory;
use App\Notification;
use App\Mail\Customer\OrderShipped;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Orchestra\Parser\Xml\Facade as XmlParser;

class OrderService
{
    public $errors = [];

    public $customer = [];

    public $order = [];

    public $order_code = null;

    public $cart =[
        'cart_items' => [],
        'total_qunatity' =>0,
        'cart_subtotal' =>0.00,
        'cart_total' =>0.00
    ];
    /**
     * @param $title
     * @param int $id
     * @return string
     * @throws \Exception
     */
    public function get()
    {
        return Order::get();
    }

        /**
     * @param $title
     * @param int $id
     * @return string
     * @throws \Exception
     */
    public function createOrder(Request $request, $user = null)
    {  
            $this->customer = $user ? $user : auth('api')->user();
            //dd($this->customer );
            $cartItems = $this->customer->getMeta('cart_data');
            $total_shipping = $this->calculateShipping($request, $cartItems);

            if ($this->customer =='') {
                $this->errors[] = 'Invalid customer.';
            }else if (empty($cartItems['cart_items']) || $cartItems == '') {
                $this->errors[] = 'Cart is empty';
            }else{
 
            $this->order_code = 'ORD'. mt_rand().$this->customer->id;

            $mainorder = new MainOrder();
            $mainorder->order_code = $this->order_code;
            $mainorder->customer_id = $this->customer->id;
            $mainorder->subtotal = $cartItems['cart_subtotal'];
            $mainorder->discount = isset($cartItems['discount']) ? $cartItems['discount'] : 0;
            $mainorder->date = date('Y-m-d') ;
            $mainorder->payment_method = $request->payment_method;
            $mainorder->payment_status = $request->payment_status;
            $mainorder->from = $request->from;
            $mainorder->save();

            foreach ($cartItems['cart_items'] as $key => $item) {  //dd($cartItems);
                $sub_order_code = 'SUB'. mt_rand().$this->customer->id;
                $items = Product::find($item['id']); 
                if ($items) {
                    $order = new Order;
                    $order->customer_id = $this->customer->id;
                    $order->main_order_id = $mainorder->id;
                    $order->order_code = $this->order_code;
                    $order->item_id = $item['id'];
                    $order->item_name = $item['name'];
                    $order->item_quantity = $item['quantity'];
                    $order->item_amount = $item['price'];
                    $order->item_total = $item['item_total'];
                    $order->status = 'Pending';
                    $order->payment_status = $request->payment_status;
                    $order->payment_method = $request->payment_method;
                    $order->sub_order_code = $sub_order_code;
                    $order->vendor_id = $items->author->id;
                    //calculate and save the commission
                    if($items->author->commission > 0){
                        $order->commission = ($order->item_total*$items->author->commission)/100;
                    }
                    $order->save();
                    if($items->product_type == 'variable'){
                        $order->setMeta('variations', $item['variations']);
                    }else{
                        $order->setMeta('attributes', $item['attributes']);
                    }

                    if(isset($cartItems['meta'])){
                        $order->setMeta('cart_meta', $cartItems['meta']);
                    }

                    $order->setMeta('item_data', $item['item_data']);
                    $order->setMeta('billing_address', $request->billing_address);
                    $order->setMeta('shipping_address', $request->shipping_address);  
                    $order->setMeta('payment_method', $request->payment_method);  
                    $order->setMeta('payment_status', $request->payment_status);                     
                    $order->setMeta('commission_percentage', $items->author->commission);                     
                    $order->setMeta('subtotal', $cartItems['cart_subtotal']);                                        
                    $order->setMeta('discount', isset($cartItems['discount']) ? $cartItems['discount'] : 0);
                    $order->setMeta('shipping_charge', $total_shipping);
                    $order->setMeta('total', ($cartItems  ['cart_total'] + $total_shipping));                                 
                    $order->save();
                    $this->manageStocks($items,$order,$item);
                    $this->order = Order::with('item')->where('order_code',$this->order_code)->get();
                }else{
                    $this->errors[] = 'Item '.$item['name'].' not found' ;
                }
            }

            $mainorder->shipping = $total_shipping;
            $mainorder->total = $cartItems['cart_total'] + $total_shipping;
            
            if(isset($cartItems['meta']['coupon'])){
                $mainorder->discount_id = $cartItems['meta']['coupon']['id'];
                $mainorder->discount_type = 'coupon';
            }else if(isset($cartItems['meta']['affiliate'])){
                $mainorder->discount_id = $cartItems['meta']['affiliate']['id'];   
                $mainorder->discount_type = 'affiliate';
            }
            
            $mainorder->save();

            $this->generate_affiliate_commision($mainorder, $cartItems);
            $this->sendNotification($request);  
            $this->customer->setMeta('cart_data',$this->cart);
            $this->customer->save();    
        }
        return array('order'=>$this->order,'errors'=>$this->errors);
    }
 

    public function sendNotification($request){
        if(empty($this->errors) && $this->order->count()> 0){
            $message ="Hi ".$this->customer->full_name.",\nKlothus has successfully received your order. Its details will be sent to you shortly.\nOrder code: ".$this->order[0]->order_code."
                \nThank You,\nKlothus.com ";

            if($this->customer->phone !=''){
                send_message($this->customer->phone,$message);
            }else if(isset($request->billing_address[0]['phone'])){
                send_message($request->billing_address[0]['phone'],$message);
            }
            
            if($this->customer->email !=''){
                Mail::to($this->customer->email)->send(new OrderShipped($this->order));
            }else if(isset($request->billing_address[0]['email'])){
                Mail::to($request->billing_address[0]['email'])->send(new OrderShipped($this->order));
            } 
        }
    }

    public function manageStocks(Product $product, Order $order, $item){

        //manage stock 
        if($product->product_type == 'variable'){
            $sku = Sku::find($item['variations']['id']);
            $sku->quantity = $sku->quantity - $item['quantity'];
            $sku->save();
        }else{
            $sku = $product->skus;
            $sku->quantity = $sku->quantity - $item['quantity'];
            $sku->save();
        }

        //genereate stock history
        $stockHistory = new StockHistory();
        $stockHistory->order_id = $order->id;
        $stockHistory->product_id = $product->id;
        $stockHistory->variation_id = $product->product_type == 'variable' ? $item['variations']['id'] : 0;
        $stockHistory->quantity = $item['quantity'];
        $stockHistory->save();
    }

    public function calculateShipping($request, $cart){
        $shipping = 0;
        if($cart['cart_total'] >= 10000){
           $shipping = 0;
        }else if (isset($request->shipping_address[0]['insidekathmandu']) && $request->shipping_address[0]['insidekathmandu']== 'Inside Valley') {
            $shipping = 100;
        }else if (isset($request->shipping_address[0]['insidekathmandu']) && $request->shipping_address[0]['insidekathmandu']== 'Outside Valley') {
            $shipping = 150;
        }
        return $shipping;
    }

    public function generate_affiliate_commision(MainOrder $order, $cartItems){

       if(isset($cartItems['meta']['affiliate'])){
            $affiliate_row = Affiliate::find($cartItems['meta']['affiliate']['id']);
            if ($affiliate_row) {
                $commision = ($cartItems['discount']*$affiliate_row->commission)/100;
                $affiliate = new AffiliateOrder();
                $affiliate->affiliate_id = $affiliate_row->id;
                $affiliate->user_id = $order->customer_id;
                $affiliate->order_id = $order->id;
                $affiliate->commision = $commision;
                //$affiliate->order_total = $totalOrderAmount;
                $affiliate->save();
            }
       }
    }
	
    public function apply_payment_method($request, $amount){

        if(strtolower($request->payment_method) == 'esewa'){
            $url = "https://uat.esewa.com.np/epay/transrec";
            $data =[
                'amt'=> $amount,
                'rid'=> '000AE01',
                'pid'=>'ee2c3ca1-696b-4cc5-a6be-2c40d929d453',
                'scd'=> 'epay_payment'
            ];

            $curl = curl_init($url);
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            $response = curl_exec($curl);
            curl_close($curl);
            $response = preg_replace("/\r|\n/", "",strip_tags($response)); 

            Order::where('order_code', $this->order_code)->update(['payment_status' => ucfirst($response)]);
            $this->errors[] = ['payment_error'=>array('message'=>'Your order has been created but the payment has been failed.','status'=>$response)];
        }
    }

}