<?php
namespace App\Http\Controllers\API;
use Cart;
use Session;
use App\Role;
use App\User;
use App\Order;
use Validator;
use App\Sku;
use App\Company;
use App\Coupon;
use App\Affiliate;
use App\Product;
use App\HoldProduct;
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

$cat_array = array();
$item_cat_array = array();
$item_cat_array2 = array();

class CartApiController extends Controller
{
    public $errors =[];

    public $cart =[
        'cart_items' => [],
        'total_qunatity' =>0,
        'cart_subtotal' =>0.00,
        'cart_total' =>0.00,
        'cart_hash' =>0
    ];

    public function getProductQuantityInCart(Request $request){
        $array =[];
        $ids =[];
        foreach ($request->get('items') as $key => $cart_item) {
            $product = Product::find($cart_item['id']);
            if (!empty($product)) {
                if($product->product_type == 'single'){
                    $array[$product->id][]  =$cart_item['quantity'];
                    $ids[$product->id] = array_sum($array[$product->id]);
                }
            }
        }
        return $ids;
    }
    public function getCartItemAttributes($id){
        $ids=0;
        $color=[];
        $size=[];
        foreach ($this->cart['cart_items'] as $key => $item) {
            if($item['type'] == 'single' && $item['id'] == $id){
                $color[] = $item['attributes']['color'];
                $size[] = $item['attributes']['size'];
            }
        }
        return array('color'=>array_unique($color),'size'=>array_unique($size));
    }

    public function getCartItemAttributes2($id){
        $ids=0;
        $array=[];
        foreach ($this->cart['cart_items'] as $key => $item) {
            if($item['type'] == 'single' && $item['id'] == $id){
                $array[$ids] = $item['attributes'];
                $array[$ids]['key'] = $key;
            }
         $ids++;   
        }
        return $array;
    }

    public function search($array, $key, $value){
        $results = array();

        if (is_array($array)) {
            if (isset($array[$key]) && $array[$key] == $value) {
                $results[] = $array;
            }

            foreach ($array as $subarray) {
                $results = array_merge($results, $this->search($subarray, $key, $value));
            }
        }

        return $results;
    }

    public function getTotalQuantityCountOnlyThatProduct($needals_attributes,$id){
        $ids=0;
        foreach ($this->cart['cart_items'] as $key => $item) {
            if($item['id'] == $id){
                if($item['attributes']['color'] == $needals_attributes['color'] && $item['attributes']['size'] == $needals_attributes['size']){
                    $ids += $item['quantity'];
                }
            }
        }
        return $ids;
    }

    public function getCartProductWithQuantityByProductId($id){
        $ids=0;
        foreach ($this->cart['cart_items'] as $key => $item) {
            if($item['id'] == $id){
                $ids += $item['quantity'];
            }
        }
        return $ids;
    }

    public function getMergedQuantityTypeProducts($id){
        $findifexists=array();
        $quantity = 0;
        foreach ($this->cart['cart_items'] as $key => $item) {
            if($item['id'] == $id && $item['type'] == 'single'){
                $quantity += $item['quantity'];
                $findifexists[$item['id']] = $quantity;
            }

            if($item['id'] == $id && $item['type'] == 'variable'){
                $findifexists[$item['id']][$item['variations']['id']] = $item['quantity'];
            }
        }
        return $findifexists;
    }
    /**
     * check hold products
     * Checks holded products while checkout
     * @Role: API
     * @param  \Illuminate\Http\Request  $request
     * @return JSON
     */    
    public function check_holded_product_while_checkout(Request $r){
        $user = auth('api')->user();
        $this->cart = $user->cart_data;
        if(isset($this->cart['cart_items']) && !empty($this->cart['cart_items'])){
            $holded_products = HoldProduct::select('item_id',\DB::raw('SUM(item_quantity) as total_holded_qunatity'),'variation_id','raw')->groupBy('item_id')->groupBy('variation_id')->get();
             
            if($holded_products->count() > 0){
                foreach ($holded_products as $key => $row) {
                    if($row->item()->exists()){
                        $_exists = $this->getMergedQuantityTypeProducts($row->item_id);
                        $total_holded_quantity_per_item = $row->total_holded_qunatity;
                        if(!empty($_exists)){
                            $product = Product::find($row->item_id);
                            if($product->product_type == 'single'){ 
                                $left_quantity_after_holded_the_product = $product->skus->quantity -$row->total_holded_qunatity;
                                if( $_exists[$row->item_id] > $left_quantity_after_holded_the_product){
                                    $varient_msg = '';
                                    $raw = json_decode($row->raw, true);
                                    if(!empty($raw['attributes'])){
                                        foreach ($raw['attributes'] as  $k=>$v) {
                                            $varient_msg .= strtolower($k).' '.strtolower($v).' and ';
                                        }
                                    }
                                    $this->errors[] = $product->name .' with '.rtrim($varient_msg,'and ').' has only '.$left_quantity_after_holded_the_product.' quantity to checkout.';
                                }
                            }else if($product->product_type == 'variable' && isset($_exists[$row->item_id][$row->variation_id])){
                                $variation = $this->getVariations($product, $row->variation_id);
                                $left_quantity_after_holded_the_product = $variation['quantity'] - $row->total_holded_qunatity;
                                if( $_exists[$row->item_id][$row->variation_id] > $left_quantity_after_holded_the_product){ 
                                    $varient_msg = '';
                                    $raw = $variation->toArray();
                                    if(!empty($raw['sku_values'])){
                                        foreach ($raw['sku_values'] as  $v) {
                                            $varient_msg .= $v['variant']['name'].' '.strtolower($v['options']['name']).' and ';
                                        }
                                    }
                                    $this->errors[] = $product->name .' with '.rtrim($varient_msg,'and ').' has only '.$left_quantity_after_holded_the_product.' quantity to checkout.';
                                }
                            }
                        }                     
                    }
                }
            }
 
            foreach ($this->cart['cart_items'] as $key => $cart) {
                $product = Product::find($cart['id']);
                if($product->status == 'Enable'){
                    if($product->product_type == 'single'){
                        $cart_quantity = $this->getCartProductWithQuantityByProductId($cart['id']);
                        $avaistock = $product->sku->quantity; // avaialbe stock
                        if($cart_quantity > $avaistock){
                            $this->errors[] = $product->name .' has only '.$avaistock.' quantity to checkout.';
                        }
                    }else if($product->product_type == 'variable'){
                        $variations = $this->getVariations($product, $cart['variations']['id']);
                        if( $cart['quantity'] > $variations['quantity']){ 
                            $this->errors[] = $product->name .' with sku '.$cart['variations']['sku'].' has only '.$variations['quantity'].' quantity to checkout.';
                        }
                    }
                }else{
                    $this->errors[] = $product->name .' has been disabled to checkout, please try again';
                }
            }

            return !empty($this->errors) ? response()->json([
                'success' => 400,
                'errors' => array_unique($this->errors)
            ],400) : response()->json([
                'success' => 200
            ]);
        }else{
            return response()->json([
                'success' => 400,
                'message' => 'Your cart is empty'
            ],400);        
        }    
    }
    /**
     * Hold products
     * Checks holded products while checkout
     * @Role: API
     * @param  \Illuminate\Http\Request  $request
     * @return JSON
     */    
    public function holdProduct(Request $r){
        $user = auth('api')->user();
        $this->cart = $user->cart_data;
        if(isset($this->cart['cart_items']) && !empty($this->cart['cart_items'])){
    
            foreach ($this->cart['cart_items'] as $key => $row) {
                
                if($row['type'] == 'single'){
                    $hp = HoldProduct::where('item_id', $row['id'])->first();
                }else{
                    $hp = HoldProduct::where(['item_id' => $row['id'],'variation_id'=> $row['variations']['id']])->first();
                }

                $hp = !empty($hp) ? $hp : new HoldProduct();
                $hp->customer_id = $user->id;
                $hp->item_id = $row['id'];
                $hp->item_name = $row['name'];
                $hp->item_quantity = $row['quantity'];
                $hp->item_amount = $row['price'];
                $hp->item_total = $row['item_total'];
                $hp->type = $row['type'];
                $hp->variation_id = isset($row['variations']) ? $row['variations']['id'] : 0;
                $hp->raw = json_encode($row);
                $hp->save();
            }

            return response()->json([
            'success' => 200,
            'message' => 'Items holded'
            ]); 
        
        }else{
            return response()->json([
                'success' => 400,
                'message' => 'Your cart is empty'
            ],400);
        }    
    }
    /**
     * unhold products
     * Checks holded products while checkout
     * @Role: API
     * @param  \Illuminate\Http\Request  $request
     * @return JSON
     */    
    public function unhold(Request $r){
        $user = auth('api')->user()->holdItems()->delete();;
    }
    /**
     * Cart store
     * Handler of user cart at that time
     * @Role: API
     * @param  \Illuminate\Http\Request  $request
     * @return JSON
     */
    public function store(Request $r, $customer = null){

        $validator = Validator::make($r->all(), [
            'id' => 'required|exists:App\Product,id',
            'quantity'=>'required|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json(['error'=>$validator->errors()], 401);            
        }
        $product = Product::find($r->id);
        if($product->product_type == 'variable' && $r->missing('variation_id')){
            return response()->json(['error'=>['variation_id'=>['variantion id is required.']]], 401);
        }else if($product->product_type == 'single' && $r->missing('attributes')){
            return response()->json(['error'=>['attributes'=>['attributes are required.']]], 401);
        }
        $is_error = false;
        $user = $customer ? $customer : auth('api')->user();
        /*$this->cart['cart_items'] = [];
        $user->setMeta('cart_data',$this->cart);
        $user->save();*/
        $cart = $user->cart_data;
        if(isset($cart['cart_items']) && !empty($cart['cart_items'])){
            $this->cart = $cart;
            foreach ($cart['cart_items'] as $key => $item) {
                $product = Product::find($item['id']); 
                $price = $product->sku->sale_price > 0 ? $product->sku->sale_price : $product->sku->regular_price; //product price
                $avaistock = $product->sku->quantity; // avaialbe stock
                //check the reqested item with cart item row
                if($product->product_type == 'single'){
                    //check product type
                    if($item['id'] == $r->id){
                    //add the total quantity 
                        $needals_attributes = $r->get('attributes');
                        $total_used_qunatity = $this->getTotalQuantityCount($needals_attributes,$item['id']);
                        $total_used_qunatity_in_current_state = $this->getTotalQuantityCountOnlyThatProduct($needals_attributes,$item['id']);
                        $total_used_qunatity = ($total_used_qunatity - $total_used_qunatity_in_current_state) + $r->quantity ;
                        //check availale stock in the system
                        if($avaistock >= $total_used_qunatity){
                            $check_attributes = array_intersect($r->get('attributes'), $item['attributes']); //match the attributes
                            $checkExistingAttribute = $this->getCartItemAttributes($item['id']);   
                            if(in_array($needals_attributes['color'],$checkExistingAttribute['color']) && in_array($needals_attributes['size'],$checkExistingAttribute['size'])){
                                if($item['attributes']['color'] == $needals_attributes['color'] && $item['attributes']['size'] == $needals_attributes['size']){
                                    $this->cart['cart_items'][$key]['quantity'] = $r->quantity;
                                    $this->cart['cart_items'][$key]['item_total'] = $price*$r->quantity; 
                                }else{
                                    $this->cart['cart_items'][$key]['quantity'] = $item['quantity'];
                                    $this->cart['cart_items'][$key]['item_total'] = $price*$item['quantity'];
                                }
                            }else{
                                    //add item into the cart if attributes doesnt matched
                                    $temp_data = [
                                    'id' => $product->id,
                                    'name' => $product->name,
                                    'price' => $price,
                                    'type' => $product->product_type,
                                    'item_total' => $price * $item['quantity'],
                                    'quantity' => $r->quantity,
                                    'attributes' => $r->get('attributes'),
                                    'item_data' => [
                                        'slug'=>$product->slug,
                                        'vendor'=>$product->author,
                                        'attatchments'=>$product->custom_meta_data()
                                    ]
                                ];
                                array_push($this->cart['cart_items'], $temp_data);
                            }  
                        }else{
                            //throw error if quantity overflowed
                            $is_error = true;
                            $this->errors[] = $product->name .' has only '.$avaistock.' quantity to checkout.';
                        }   
                    }else{
                        if(!in_array($r->id, $this->getIdsCount())){
                             $newProduct = Product::find($r->id);
                                if($newProduct->product_type == 'single'){
                                        if($newProduct->sku->quantity >= $r->quantity){
                                        $price = $newProduct->sku->sale_price > 0 ? $newProduct->sku->sale_price : $newProduct->sku->regular_price; //product price
                                            $temp_data = [
                                                'id' => $newProduct->id,
                                                'name' => $newProduct->name,
                                                'price' => $price,
                                                'type' => $newProduct->product_type,
                                                'item_total' => $price * $r->quantity,
                                                'quantity' => $r->quantity,
                                                'attributes' => $r->get('attributes'),
                                                'item_data' => [
                                                    'slug'=>$newProduct->slug,
                                                    'vendor'=>$newProduct->author,
                                                    'attatchments'=>$newProduct->custom_meta_data()
                                                ]
                                            ]; 
                                        array_push($this->cart['cart_items'], $temp_data);                      
                                    }else{
                                        $is_error = true;
                                        $this->errors[] = $product->name .' has only '.$avaistock.' quantity to checkout.';
                                    }                                    
                                }else{
                                    $variations = $this->getVariations($newProduct, $r->variation_id); 
                                    if( $variations['quantity'] >= $r->quantity){
                                    $price = $variations['sale_price'] > 0 ? $variations['sale_price'] : $variations['regular_price'];
                                    $temp_data = [
                                        'id' => $newProduct->id,
                                        'name' => $newProduct->name,
                                        'price' => $price,
                                        'type' => $newProduct->product_type,
                                        'item_total' => $price * $r->quantity,
                                        'quantity' => $r->quantity,
                                        'variations' =>$variations,
                                        //'variants' => $data,
                                        'item_data' => [
                                            'slug'=>$newProduct->slug,
                                            'vendor'=>$newProduct->author,
                                            'attatchments'=>$newProduct->custom_meta_data()
                                        ]
                                    ];
                                    array_push($this->cart['cart_items'], $temp_data);
                                    }else{
                                        $is_error = true;
                                        $this->errors[] = $product->name .' has only '.$variations['quantity'].' quantity to checkout.';
                                    }
                                }
                        }
                    }
                }else if($product->product_type == 'variable'){
                    //check variable producr having the same id in the cart
                    if($item['id'] == $r->id){ 
                            //in_array($r->variation_id, $totalVariableProducts)
                            //check for the varation product exists in the cart and update the quantity
                        if(isset($item['variations'])){
                            if($r->variation_id == $item['variations']['id']){
                                $variations = $this->getVariations($product, $item['variations']['id']);
                                $quantity = $r->quantity;
                                if( $variations['quantity'] >= $quantity){ 
                                    $price = $variations['sale_price'] > 0 ? $variations['sale_price'] : $variations['regular_price'];    
                                    $this->cart['cart_items'][$key]['quantity'] = $quantity;
                                    $this->cart['cart_items'][$key]['item_total'] = $price * $quantity;
                                }else{
                                    $is_error = true;
                                    $this->errors[] = $product->name .' has only '.$variations['quantity'].' quantity to checkout.';
                                }
                            }else if(!in_array($r->variation_id, $this->getTotalVariationsCount())){
                            //if variations not exists add new
                            $variations = $this->getVariations($product, $r->variation_id);     
                            if($variations){    
                                if( $variations['quantity'] >= $r->quantity){
                                    $price = $variations['sale_price'] > 0 ? $variations['sale_price'] : $variations['regular_price'];
                                    $temp_data = [
                                        'id' => $product->id,
                                        'name' => $product->name,
                                        'price' => $price,
                                        'type' => $product->product_type,
                                        'item_total' => $price * $r->quantity,
                                        'quantity' => $r->quantity,
                                        'variations' =>$variations,
                                        'item_data' => [
                                            'slug'=>$product->slug,
                                            'vendor'=>$product->author,
                                            'attatchments'=>$product->custom_meta_data()
                                        ]
                                    ];
                                    array_push($this->cart['cart_items'], $temp_data);
                                }else{
                                    $is_error = true;
                                    $this->errors[] = $product->name .' has only '.$variations['quantity'].' quantity to checkout.';
                                }
                            }                       
                        }
                    }
                }else{
                    if(!in_array($r->variation_id, $this->getTotalVariationsCount())){
                        //if variations not exists add new  
                            $newProduct = Product::find($r->id);
                            if($newProduct->product_type == 'single'){
                                if(!in_array($r->id, $this->getIdsCount())){
                                    if($newProduct->sku->quantity >= $r->quantity){
                                        $price = $newProduct->sku->sale_price > 0 ? $newProduct->sku->sale_price : $newProduct->sku->regular_price; //product price
                                        $this->cart['cart_items'][] = [
                                            'id' => $newProduct->id,
                                            'name' => $newProduct->name,
                                            'price' => $price,
                                            'type' => $newProduct->product_type,
                                            'item_total' => $price * $r->quantity,
                                            'quantity' => $r->quantity,
                                            'attributes' => $r->get('attributes'),
                                            'item_data' => [
                                                'slug'=>$newProduct->slug,
                                                'vendor'=>$newProduct->author,
                                                'attatchments'=>$newProduct->custom_meta_data()
                                            ]
                                        ];                       
                                    }else{
                                        $is_error = true;
                                        $this->errors[]= $product->name .' has only '.$avaistock.' quantity to checkout.';
                                    }
                                }                                   
                            }else{
                                $variations = $this->getVariations($newProduct, $r->variation_id); 
                                if( $variations['quantity'] >= $r->quantity){
                                $price = $variations['sale_price'] > 0 ? $variations['sale_price'] : $variations['regular_price'];
                                $temp_data = [
                                    'id' => $newProduct->id,
                                    'name' => $newProduct->name,
                                    'price' => $price,
                                    'type' => $newProduct->product_type,
                                    'item_total' => $price * $r->quantity,
                                    'quantity' => $r->quantity,
                                    'variations' =>$variations,
                                    //'variants' => $data,
                                    'item_data' => [
                                        'slug'=>$newProduct->slug,
                                        'vendor'=>$newProduct->author,
                                        'attatchments'=>$newProduct->custom_meta_data()
                                    ]
                                ];
                                array_push($this->cart['cart_items'], $temp_data);
                                }else{
                                    $is_error = true;
                                    $this->errors[] = $product->name .' has only '.$variations['quantity'].' quantity to checkout.';
                                }
                            }
                        }                       
                    }  
                }//check variable product
            }//endforeach
        }else{ //check if user having the product
            //check if user dont have cart data  
            if (!empty($product)) {
                    if($product->status == 'Enable'){
                    if($product->product_type == 'variable'){      
                        $variations = $this->getVariations($product, $r->variation_id); 
                        if( $variations['quantity'] >= $r->quantity){
                            $price = $variations['sale_price'] > 0 ? $variations['sale_price'] : $variations['regular_price'];
                            $this->cart['cart_items'][] = [
                                    'id' => $product->id,
                                    'name' => $product->name,
                                    'price' => $price,
                                    'type' => $product->product_type,
                                    'item_total' => $price * $r->quantity,
                                    'quantity' => $r->quantity,
                                    'variations' =>$variations,
                                    //'variants' => $data,
                                    'item_data' => [
                                        'slug'=>$product->slug,
                                        'vendor'=>$product->author,
                                        'attatchments'=>$product->custom_meta_data()
                                    ]
                                ];
                            }else{
                                $is_error = true;
                                $this->errors[] = $product->name .' has only '.$variations['quantity'].' quantity to checkout.';
                            }
                        
                    }else if($product->product_type == 'single'){
                        $avaistock = $product->sku->quantity; //get the stock
                        //check the total stock first
                        if($product->sku->quantity >= $r->quantity){
                            $price = $product->sku->sale_price > 0 ? $product->sku->sale_price : $product->sku->regular_price; //product price
                            $this->cart['cart_items'][] = [
                                    'id' => $product->id,
                                    'name' => $product->name,
                                    'price' => $price,
                                    'type' => $product->product_type,
                                    'item_total' => $price * $r->quantity,
                                    'quantity' => $r->quantity,
                                    'attributes' => $r->get('attributes'),
                                    'item_data' => [
                                        'slug'=>$product->slug,
                                        'vendor'=>$product->author,
                                        'attatchments'=>$product->custom_meta_data()
                                ]
                            ];                       
                        }else{
                            $is_error = true;
                            $this->errors[]= $product->name .' has only '.$avaistock.' quantity to checkout.';
                        } 
                    }else{
                        $is_error = true;
                        $this->errors[] = 'Invalid product type';
                    }
                }else{
                    $this->errors[] = $product->name .' is already been disabled.';
                }

            }else{
                $is_error = true;
                $this->errors[] = 'Invalid product';
            }
        }
        //unset($this->cart['cart_items'][1]);
          // $this->cart['cart_items'] = [];
        $this->cart['total_qunatity'] = array_sum(array_column($this->cart['cart_items'], 'quantity'));
        $this->cart['cart_subtotal'] = array_sum(array_column($this->cart['cart_items'], 'item_total'));
        $this->cart['cart_total'] = array_sum(array_column($this->cart['cart_items'], 'item_total'));
        
        $this->cart['cart_hash'] = substr(md5(time()), 0, 10);
        $user->setMeta('cart_data',$this->cart);
        $user->save();
        return $is_error == true ? response()->json([
            'success' => 400,
            'errors'  => array_unique($this->errors),
            'results' =>$this->cart,
            'message' =>'Cart has been added.'
        ],400) : response()->json([
            'success' => 200,
            'results' =>$this->cart,
            'message' =>'Cart has been added.'
        ]);
    }

    public function getIdsCount(){
        $ids=[];
        foreach ($this->cart['cart_items'] as $key => $item) {
            if(isset($item['id'])){
                $ids[]= $item['id'];
            }
        }
        return $ids;
    }

    public function getTotalQuantityCount($needals_attributes,$id){
        $ids=0;
        foreach ($this->cart['cart_items'] as $key => $item) {
            if($item['id'] == $id){
                //if($item['attributes']['color'] == $needals_attributes['color'] && $item['attributes']['size'] == $needals_attributes['size']){
                    $ids += $item['quantity'];
                //}
            }
        }
        return $ids;
    }
    public function getTotalVariationsCount(){
        $ids=[];
        foreach ($this->cart['cart_items'] as $key => $item) {
            if($item['type'] == 'variable'){
                if(isset($item['variations'])){
                    $ids[] = $item['variations']['id'];
                }
            }
        }
        return array_unique($ids);
    }
    public function getVariations($product,$id){
        $variations = $product->skusVary->where('id',$id)->first();
        if ($variations) {
            $data = [];
            $i=0;
            $j=0;
            foreach ($variations->skuValues as $skuValues) { 
                    if (!empty($skuValues->options)) {
                        $data[$j][$skuValues->variant->name] = $skuValues->options->name;
                        $j++;
                    }
                }
                $data = $variations->toArray();
            $i++;
        }
        return $variations;
    }
    /**
     * Vendor store cart
     * @Role: API
     * @param  \Illuminate\Http\Request  $request
     * @return JSON
     */
    public function store111(Request $request)
    {  
        $validator = Validator::make($request->all(), [
                'items' => 'required'
        ]);
        
        if ($validator->fails()) {
            return response()->json(['error'=>$validator->errors()], 401);            
        }
         
        $user = auth('api')->user();
        
        if($user){
            $data = [];
            $i=0;
            $j=0;
            $price=0;
            //$user_cart = $user->getMeta('cart_data') != '' ? $user->getMeta('cart_data')['cart_items'] : [];
            $existsQuantity = $this->getProductQuantityInCart($request);
            //dd($existsQuantity);
            $array = [];
            $aaaa = [];
            foreach ($request->get('items') as $key => $cart_item) {               
            $product = Product::find($cart_item['id']);

            if (!empty($product)) {
                if($product->product_type == 'variable'){
                    if(isset($cart_item['variation_id'])){
                    $variations = $product->skusVary->where('id',$cart_item['variation_id'])->first();
                    if ($variations) {
                    $data = [];
                    $i=0;
                    $j=0;
                        foreach ($variations->skuValues as $key => $skuValues) { 
                                if (!empty($skuValues->options)) {
                                    $data[$j][$skuValues->variant->name] = $skuValues->options->name;
                                    $j++;
                                }
                            }
                            $data = $variations->toArray();
                        $i++;
                    }
                    $price = $variations['sale_price'] > 0 ? $variations['sale_price'] : $variations['regular_price'];

                    if( $variations['quantity'] >= $cart_item['quantity']){
                        $this->cart['cart_items'][] = [
                                'id' => $product->id,
                                'name' => $product->name,
                                'price' => $price,
                                'type' => $product->product_type,
                                'item_total' => $price * $cart_item['quantity'],
                                'quantity' => $cart_item['quantity'],
                                'variations' =>$variations,
                                //'variants' => $data,
                                'item_data' => [
                                    'slug'=>$product->slug,
                                    'attatchments'=>$product->custom_meta_data()
                                ]
                            ];
                        }else{
                            $this->errors[] = $product->name .' has only '.$variations['quantity'].' quantity to checkout.';
                        }
                    }else{
                        $this->errors[] = 'Please put the variation_id to add the variable product into the cart.';
                    }
                }else{

                    $price = $product->sku->sale_price > 0 ? $product->sku->sale_price : $product->sku->regular_price;
                    
                    $array[$product->id][] = $cart_item['quantity'];
                    $quantity[$product->id] = 0;
                     
                    $avaistock = $product->sku->quantity;
                    $reqStock = array_sum($array[$product->id]);
                    $cacheStock = isset($aaaa[$product->id]) ? array_sum($aaaa[$product->id]) :$product->sku->quantity;
 
                    if($product->sku->quantity >= array_sum($array[$product->id])){
                         $aaaa[$product->id][] = -$product->sku->quantity;
                        $this->cart['cart_items'][] = [
                                'id' => $product->id,
                                'name' => $product->name,
                                'price' => $price,
                                'type' => $product->product_type,
                                'item_total' => $price * $cart_item['quantity'],
                                'quantity' => $cart_item['quantity'],
                                'attributes' => isset($cart_item['attributes']) ? $cart_item['attributes'] : '',
                                'item_data' => [
                                    'slug'=>$product->slug,
                                    'attatchments'=>$product->custom_meta_data()
                                ]
                            ];
                        
                    }else if($product->sku->quantity <= array_sum($array[$product->id])){
 
                        if($product->sku->quantity <= $cacheStock){
                            $aaaa[$product->id][] = -$product->sku->quantity;
                            $this->cart['cart_items'][] = [
                                    'id' => $product->id,
                                    'name' => $product->name,
                                    'price' => $price,
                                    'type' => $product->product_type,
                                    'item_total' => $price * $product->sku->quantity,
                                    'quantity' => $product->sku->quantity,
                                    'attributes' => isset($cart_item['attributes']) ? $cart_item['attributes'] : '',
                                    'item_data' => [
                                        'slug'=>$product->slug,
                                        'attatchments'=>$product->custom_meta_data()
                                    ]
                                ];
                            }
                    }else{
                        $this->errors[] = $product->name .' has only '.$product->sku->quantity.' quantity to checkout.';
                    }

                    
                }
            }else{
                 $this->errors[] = 'Invalid product';
            }
            
            }
       
             
            $this->cart['total_qunatity'] = array_sum(array_column($this->cart['cart_items'], 'quantity'));
            $this->cart['cart_subtotal'] = array_sum(array_column($this->cart['cart_items'], 'item_total'));
            $this->cart['cart_total'] = array_sum(array_column($this->cart['cart_items'], 'item_total'));
            $this->cart['cart_hash'] = substr(md5(time()), 0, 10);
            $user->setMeta('cart_data',$this->cart);
            $user->save();  
            if(count($this->errors) > 0){
                return response()->json([
                    'success' => 400,
                    'results' =>$this->cart,
                    'warnings'=>array_unique($this->errors),
                ],400);
            }else{
                return response()->json([
                    'success' => 200,
                    'results' =>$this->cart,
                    'message' =>'Cart has been added.'
                ]);
            }         
        }else{
            return response()->json([
                    'success' => 400,
                    'message' =>'Invalid customer.'
            ],400);
        }  
    }


    public function get_total_item_quantity_in_cart($item){ 
        $user = auth('api')->user();
        $user_cart = $user->getMeta('cart_data');
        $user_cart =  isset($user_cart['cart_items']) ? $user_cart['cart_items'] : [];
        $total_item_qunatity = 0;
        if(!empty($user_cart)){
            foreach ($user_cart as $key_ => $value3) {
                $total_item_qunatity += $value3['id'] == $item['id'] ? $value3['quantity'] : 0;
            }
        }
        return $total_item_qunatity;
    }

    /**
     * Vendor store cart
     * @Role: API
     * @param  \Illuminate\Http\Request  $request
     * @return JSON
     */
    public function store1(Request $request)
    {
        $validator = Validator::make($request->all(), [
                'items' => 'required'
        ]);
        
        if ($validator->fails()) {
            return response()->json(['error'=>$validator->errors()], 401);            
        }
         
        $user = auth('api')->user();
        if($user){
            $data = [];
            $i=0;
            $j=0;
            $price=0;
            $temp_flag = true;
            $left_quantity = 0;
            $user_cart = $user->getMeta('cart_data');
            $user_cart =  isset($user_cart['cart_items']) ? $user_cart['cart_items'] : [];
           
            foreach ($request->get('items') as $key => $cart_item) {       
                   
            $product = Product::find($cart_item['id']);
            if (!empty($product)) {
                if($product->product_type == 'variable'){
                    if(isset($cart_item['variation_id'])){
                    $variations = $product->skusVary->where('id',$cart_item['variation_id'])->first();
                    if ($variations) {
                    $data = [];
                    $i=0;
                    $j=0;
                        foreach ($variations->skuValues as $key => $skuValues) { 
                                if (!empty($skuValues->options)) {
                                    $data[$j][$skuValues->variant->name] = $skuValues->options->name;
                                    $j++;
                                }
                            }
                            $data = $variations->toArray();
                        $i++;
                    }
                    $price = $variations['sale_price'] > 0 ? $variations['sale_price'] : $variations['regular_price'];

                    if( $variations['quantity'] >= $cart_item['quantity']){

                        $this->cart['cart_items'][] = [
                                'id' => $product->id,
                                'name' => $product->name,
                                'price' => $price,
                                'type' => $product->product_type,
                                'item_total' => $price * $cart_item['quantity'],
                                'quantity' => $cart_item['quantity'],
                                'variations' =>$variations,
                                //'variants' => $data,
                                'item_data' => [
                                    'slug'=>$product->slug,
                                    'attatchments'=>$product->custom_meta_data()
                                ]
                            ];
                        }else{
                            $this->errors[] = $product->name .' has only '.$variations['quantity'].' quantity to checkout.';
                        }
                    }else{
                        $this->errors[] = 'Please put the variation_id to add the variable product into the cart.';
                    }
                }else{
                     
                    $price = $product->sku->sale_price > 0 ? $product->sku->sale_price : $product->sku->regular_price;               
                   // echo $_calculate_total_item_quantity;
                     //check the quantity it seldd(f
                        if( !empty($user_cart) ){
                            $tempitems = [];
                            
                            foreach ($user_cart as $k => $uItem) {

                                if($uItem['id'] == $cart_item['id']){
                                    echo $left_quantity;
                                    $check_attribute = array_intersect($uItem['attributes'], $cart_item['attributes']);
                                    $left_quantity  = $product->sku->quantity - intval($cart_item['quantity']);
                                    if(count($check_attribute) == count($cart_item['attributes']) && $temp_flag){
                                        $temp_flag = false;
                                          
                                        
                                        $tempitems = [
                                            'id' => $product->id,
                                            'name' => $product->name,
                                            'price' => $price,
                                            'type' => $product->product_type,
                                            'item_total' => $price * $cart_item['quantity'],
                                            'quantity' => $cart_item['quantity'],
                                            'attributes' => isset($cart_item['attributes']) ? $cart_item['attributes'] : '',
                                            'item_data' => [
                                                'slug'=>$product->slug,
                                                'attatchments'=>$product->custom_meta_data()
                                            ]
                                        ];

                                    }else if($left_quantity >=2){
                                       
                                       // $left_quantity  = $left_quantity - intval($cart_item['quantity']);
                                         
                                        $tempitems = [
                                            'id' => $product->id,
                                            'name' => $product->name,
                                            'price' => $price,
                                            'type' => $product->product_type,
                                            'item_total' => $price * $cart_item['quantity'],
                                            'quantity' => $cart_item['quantity'],
                                            'attributes' => isset($cart_item['attributes']) ? $cart_item['attributes'] : '',
                                            'item_data' => [
                                                'slug'=>$product->slug,
                                                'attatchments'=>$product->custom_meta_data()
                                            ]
                                        ];

                                    }else{ 
                                        echo "hi";

                                       //$this->errors[] = 'You\'re requesting for '.$cart_item['quantity'].' quantity but the product '.$product->name .' has only '.($cart_item['quantity'] - $product->sku->quantity).' quantity to checkout.';
                                    }
                                }else{
                                    /*$tempitems = [
                                            'id' => $product->id,
                                            'name' => $product->name,
                                            'price' => $price,
                                            'type' => $product->product_type,
                                            'item_total' => $price * $cart_item['quantity'],
                                            'quantity' => $cart_item['quantity'],
                                            'attributes' => isset($cart_item['attributes']) ? $cart_item['attributes'] : '',
                                            'item_data' => [
                                                'slug'=>$product->slug,
                                                'attatchments'=>$product->custom_meta_data()
                                            ]
                                        ];*/
                                }
                            }

                            if(!empty($tempitems)){
                                $this->cart['cart_items'][] = $tempitems;
                            }
                        }else{
                        $this->cart['cart_items'][] = [
                                'id' => $product->id,
                                'name' => $product->name,
                                'price' => $price,
                                'type' => $product->product_type,
                                'item_total' => $price * $cart_item['quantity'],
                                'quantity' => $cart_item['quantity'],
                                'attributes' => isset($cart_item['attributes']) ? $cart_item['attributes'] : '',
                                'item_data' => [
                                    'slug'=>$product->slug,
                                    'attatchments'=>$product->custom_meta_data()
                                ]
                            ];
                        }
                    }
                
            }else{
                 return response()->json([
                    'success' => 400,
                    'message' =>'Invalid product id.'
            ],400);
            }
            
            }
              
           
                $this->cart['total_qunatity'] = array_sum(array_column($this->cart['cart_items'], 'quantity'));
                $this->cart['cart_subtotal'] = array_sum(array_column($this->cart['cart_items'], 'item_total'));
                $this->cart['cart_total'] = array_sum(array_column($this->cart['cart_items'], 'item_total'));
                $this->cart['cart_hash'] = substr(md5(time()), 0, 10);
                $user->setMeta('cart_data',$this->cart);
                $user->save();       
                return response()->json([
                    'success' => true,
                    'results' =>$this->cart,
                    'warnings'=>$this->errors,
                    'message' =>'Cart has been added.'
                ]);
            
        }else{
            return response()->json([
                    'success' => 400,
                    'message' =>'Invalid customer.'
            ],400);
        }  
    }


    /**
     * Get cart data of specific customer
     * @Role: API
     * @param  \Illuminate\Http\Request  $request
     * @return JSON
     */
    public function cart(Request $request)
    {
        $user = auth('api')->user();
        if($user){
            return response()->json([
                'success' => true,
                'results' =>$user->getMeta('cart_data'),
                'message' =>'Cart has been loaded.'
            ]);
        }else{
            return response()->json([
                    'success' => 400,
                    'message' =>'Invalid customer.'
            ],400);
        } 
    }

    
    /**
     * delete cart data of specific customer
     * @Role: API
     * @param  \Illuminate\Http\Request  $request
     * @return JSON
     */
    public function remove(Request $request){

        $user = auth('api')->user();
        $validator = Validator::make($request->all(), [
                'customer_id' => 'required',
                'item_id' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['error'=>$validator->errors()], 401);            
        }

        if($user){
            $cartData = $user->getMeta('cart_data');

            if(!empty($cartData['cart_items'])){
             foreach ($cartData['cart_items'] as $key=>$value) {

                if ($request->get('item_id') == $value['id']) {

                    if ($request->get('attributes') && $value['type'] == 'single') {
                        $matched =  array_intersect($value['attributes'], $request->get('attributes'));
                        if (count($matched) == count($value['attributes'])) {
                            unset($cartData['cart_items'][$key]);
                        }else{
                            $this->cart['cart_items'][] = $value;
                        }
                    }else if ($request->get('variation_id') && $value['type'] == 'variable' ) {
                        if($request->get('variation_id') == $value['variations']['id']){
                            unset($cartData['cart_items'][$key]);
                        }else{
                            $this->cart['cart_items'][] = $value;
                        }
                    }
                }else{
                    $this->cart['cart_items'][] = $value;  
                }  
            }
            $this->cart['total_qunatity'] = array_sum(array_column($this->cart['cart_items'], 'quantity'));
            $this->cart['cart_subtotal'] = array_sum(array_column($this->cart['cart_items'], 'item_total'));
            $this->cart['cart_total'] = array_sum(array_column($this->cart['cart_items'], 'item_total'));
            $this->cart['cart_hash'] = substr(md5(time()), 0, 10);
            $user->setMeta('cart_data',$this->cart);
            $user->save(); 

            return response()->json([
                'success' => 200,
                'results' =>$this->cart,
                'message' =>'Item has been removed.'
            ]);

            }else{
                return response()->json([
                    'success' => 400,
                    'message' =>'Your cart is empty.'
                ],400); 
             }
            }else{
                return response()->json([
                    'success' => 400,
                    'message' =>'Invalid customer.'
            ],400);
        } 
    }

    public function _calculate_criteria_for_coupon_existance_in_cart(){

        $response['ristricted'] = true;

        $applied_product_total = 0;

        $products = array_column($this->cart['cart_items'],'id');

        $coupon_brands = $this->coupon->brands->pluck('id')->toArray();

        if($products){

            $items = Product::with('brands')->whereIn('id',$products)->get();

            if (!empty($items)) {

                foreach ($items as $key => $value) {
                    
                     foreach ($this->cart['cart_items'] as $cart_items) {

                        $this->item_cat_array2 = array();
                        $categories = $value->categories()->with('nestedChildren')->get()->toArray();
                        $legacy_get_children_of_item  = $this->getChildrenOfItemsForProduct($categories);
                        $checkcategories = array_intersect($legacy_get_children_of_item,$this->appliedCouponAssignedCategories());

                        $itemBrands = $value->brands()->first()->id;

                        $checkproduct = array_intersect($this->coupon->products->pluck('id')->toArray(),array_column($this->cart['cart_items'],'id'));
                        //checks assigned brands, categories, products existance
                        if($this->coupon->brands->count() > 0 && $this->coupon->categories->count() && $this->coupon->products->count() >0){
                            
                            if($cart_items['id'] == $value->id && in_array($itemBrands, $coupon_brands) && !empty($checkcategories) && !empty($checkproduct)){
                                $applied_product_total += $cart_items['item_total'];  
                            }
                        //checks assigned brands, categories existance
                        }else if($this->coupon->brands->count()  >0 && $this->coupon->categories->count() >0 ){
  
                            if($cart_items['id'] == $value->id && in_array($itemBrands, $coupon_brands) && !empty($checkcategories)){
                                $applied_product_total += $cart_items['item_total'];  
                            }
                        //checks assigned categories, product existance
                        }else if($this->coupon->categories->count() >0 && $this->coupon->products->count() >0){
                            
                            if($cart_items['id'] == $value->id && !empty($checkcategories) && !empty($checkproduct)){
                                $applied_product_total += $cart_items['item_total'];  
                            }
                        //checks assigned brands, product existance
                        }else if($this->coupon->brands->count() >0 && $this->coupon->products->count() >0){ 
                            
                            if($cart_items['id'] == $value->id && in_array($itemBrands, $coupon_brands) && !empty($checkproduct)){
                                $applied_product_total += $cart_items['item_total'];  
                            }
                        //checks assigned categories existance
                        }else if($this->coupon->categories->count() >0){ 

                            if($cart_items['id'] == $value->id && !empty($checkcategories)){
                                $applied_product_total += $cart_items['item_total'];
                            }
                        //checks assigned coupon brand existance
                        }else if($this->coupon->brands->count() > 0){ 
                            
                            if($cart_items['id'] == $value->id && in_array($itemBrands, $coupon_brands)){
                                $applied_product_total += $cart_items['item_total'];  
                            }
                        //checks assigned coupon product existance 
                        }else if($this->coupon->products->count() >0){ 
                            
                            if($cart_items['id'] == $value->id  && !empty($checkproduct)){
                                $applied_product_total += $cart_items['item_total'];  
                            }

                        }else{
                            //no ristrictions
                            $response['ristricted'] = false;
                            if($cart_items['id'] == $value->id){
                                $applied_product_total += $cart_items['item_total'];
                            }
                        }
                    }
                }
            }
        }
        $response['applied_product_total'] = $applied_product_total;
        return $response;
    }

    /**
     * apply coupon 
     * @Role: API
     * @param  \Illuminate\Http\Request  $request
     * @return JSON
     */
    public function apply_coupon(Request $request){
        $validator = Validator::make($request->all(), [
            'coupon_code' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['error'=>$validator->errors()], 401);            
        }


        $user = auth('api')->user();
        $this->cart = $user->cart_data;

        if(isset($this->cart['cart_items']) && empty($this->cart['cart_items'])){

        return response()->json([
                        'success' => 401,
                        'message' =>'Your cart is empty.'
                    ],401);
        }

        $this->coupon = Coupon::whereCode($request->get('coupon_code'))->first();

        if (!$this->coupon) {

            return response()->json([
                    'success' => 400,
                    'message' =>'Invalid coupon code.'
            ],400);

        }else if ($this->coupon->status == 'Disable') {
            return response()->json([
                    'success' => 400,
                    'message' =>'Applied coupon is inactive, please try again.'
            ],400);
        }else{

            if($this->coupon->start_date !='' && $this->coupon->expiry_date !=''){

                if($this->coupon->start_date <= date('Y-m-d') && $this->coupon->expiry_date >= date('Y-m-d')){
                     
                    $alreadyUsed = $user->mainOrders()->whereHas('coupons', function($q){
                        $q->whereCode($this->coupon->code);
                    })->count();

                    if($alreadyUsed){
                        return response()->json([
                            'success' => 400,
                            'message' =>'You have already used this coupon.'
                        ],400);
                    }else{
                        $response = $this->_calculate_criteria_for_coupon_existance_in_cart();

                        if($response['ristricted'] == true && $response['applied_product_total'] == 0){

                            return response()->json([
                                'success' => 400,
                                'message' =>'This coupon code is not valid for selected cart items.'
                            ],400);

                        }else{

                            $this->cart = $this->apply_coupon_and_recalculate_cart($user, $response['applied_product_total']);
                            
                            return response()->json([
                                'success' => 200,
                                'results' =>$this->cart,
                                'message' =>'Coupon has been applied'
                            ]);                        
                        }
                    }
                }else{
                    return response()->json([
                        'success' => 400,
                        'message' =>'Applied coupon has already been expired.'
                    ],400);
                }

            }else if($this->coupon->expiry_date > date('Y-m-d')){

                $response = $this->_calculate_criteria_for_coupon_existance_in_cart();

                if($response['ristricted'] == true && $response['applied_product_total'] == 0){

                    return response()->json([
                        'success' => 400,
                        'message' =>'This coupon code is not valid for selected cart items.'
                    ],400);

                }else{

                    $this->cart = $this->apply_coupon_and_recalculate_cart($user, $response['applied_product_total']);
                    
                    return response()->json([
                        'success' => 200,
                        'results' =>$this->cart,
                        'message' =>'Coupon has been applied'
                    ]);                        
                }

            }else{
                return response()->json([
                    'success' => 400,
                    'message' =>'Applied coupon has already been expired.'
                ],400);
            }  
        }
    }

    /**
     * apply coupon 
     * @Role: API
     * @param  \Illuminate\Http\Request  $request
     * @return JSON
     */
    public function apply_coupon_BAKUP(Request $request){
        $validator = Validator::make($request->all(), [
            'coupon_code' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['error'=>$validator->errors()], 401);            
        }


        $user = auth('api')->user();
        $this->cart = $user->cart_data;

        if(isset($this->cart['cart_items']) && empty($this->cart['cart_items'])){

        return response()->json([
                        'success' => 401,
                        'message' =>'Your cart is empty.'
                    ],401);
        }

        $coupon = Coupon::whereCode($request->get('coupon_code'))->first();

        if ($coupon) {

            if ($coupon->status == 'Enable') {
               
                if($coupon->expiry_date > date('Y-m-d')){

                    if ($coupon->products->count() > 0 || $coupon->categories->count() > 0) {
                        $category = $this->get_cart_item_categories() ? $this->get_cart_item_categories() : [];

                        $_check_product_existance_categories = array_intersect($category,$this->appliedCouponAssignedCategories($coupon));

                        $_check_product_existance_product = array_intersect($coupon->products->pluck('id')->toArray(),array_column($this->cart['cart_items'],'id'));
                        
                        
                        if (!empty($_check_product_existance_categories)) {
                            
                            $this->cart = $this->apply_coupon_and_recalculate_cart($coupon, $user, $this->cart, $_check_product_existance_categories);

                            return response()->json([
                                'success' => 200,
                                'results' =>$this->cart,
                                'message' =>'Coupon has been applied'
                            ]);

                        }else if (!empty($_check_product_existance_product)) {
                            
                            $this->cart = $this->apply_coupon_and_recalculate_cart($coupon, $user, $this->cart, $_check_product_existance_product);

                            return response()->json([
                                'success' => 200,
                                'results' =>$this->cart,
                                'message' =>'Coupon has been applied'
                            ]);

                        }else{
                             return response()->json([
                                'success' => 400,
                                'message' =>'This coupon is not valid for selected items.'
                            ],400);
                        }
                    }else{

                        $this->cart = $this->apply_coupon_and_recalculate_cart($coupon, $user, $this->cart, $_check_product_existance = ['Yes']);
                            return response()->json([
                                'success' => 200,
                                'results' =>$this->cart,
                                'message' =>'Coupon has been applied'
                        ]);    
                    }
                }else{
                    return response()->json([
                        'success' => 400,
                        'message' =>'Applied coupon has already been expired.'
                    ],400);
                }
            }else{

                return response()->json([
                    'success' => 400,
                    'message' =>'Applied coupon is inactive, please try again.'
                ],400);

            }         
        }else{

            return response()->json([
                    'success' => 400,
                    'message' =>'Invalid coupon code.'
            ],400);
            
        }
    }

    protected function apply_coupon_and_recalculate_cart($user, $applied_product_total){

        if(isset($this->cart['meta']['affiliate'])){
            unset($this->cart['meta']['affiliate']);
        }
        if($this->coupon->discount_type == 'Fixed'){
            $discount = $this->coupon->amount;
        }else if($this->coupon->discount_type == 'Percentage'){
            $discount = round(($applied_product_total*$this->coupon->percentage)/100,2);
        }else{
            $discount = 0;
        }
 
        $_calculate_total = $this->cart['cart_subtotal'] - $discount;
        $this->cart['discount'] = $discount;
        $this->cart['cart_total'] =  $_calculate_total;
        $this->cart['meta']['coupon']['id'] = $this->coupon->id;
        $this->cart['meta']['coupon']['code'] = $this->coupon->code;
        $this->cart['meta']['coupon']['type'] = $this->coupon->discount_type;
        
        if($this->coupon->discount_type == 'Percentage'){
            $this->cart['meta']['coupon']['percentage'] = $this->coupon->percentage;
        }else{
            $this->cart['meta']['coupon']['amount'] = $this->coupon->amount;
        }
        $user->setMeta('cart_data', $this->cart);
        $user->save();
        return $this->cart;
    }

    public function appliedCouponAssignedCategories(){
        $categories = $this->coupon->categories()->with('nestedChildren')->get()->toArray();
        return $categories ? $this->getChildren($categories) : [];
    }

    public function appliedAffiliateAssignedCategories(){
        $categories = $this->affiliate->categories()->with('nestedChildren')->get()->toArray();
        return $categories ? $this->getChildren($categories) : [];
    }

    public function getChildren($categories1){
        global $cat_array;
            foreach($categories1 as $category){
                $cat_array[]  = $category['id'];
                if (!empty($category['nested_children'])) {
                    $this->getChildren($category['nested_children']);    
                }
        }
        return $cat_array;
    }

    public function getChildrenOfItems($categories1){
        global $item_cat_array;
            foreach($categories1 as $category){
                $item_cat_array[]  = $category['id'];
                if (!empty($category['nested_children'])) {
                    $this->getChildrenOfItems($category['nested_children']);    
                }
        }
        return $item_cat_array;
    }
 

    public function get_cart_item_categories(){
        $categories2 = array();
        $products = array_column($this->cart['cart_items'],'id');
        if($products){
            $items = Product::with('categories')->whereIn('id',$products)->get();
            if (!empty($items)) {
                foreach ($items as $key => $value) {
                    $categories2[] = $value->categories()->with('nestedChildren')->first()->toArray();        
                }
            }

            return $this->getChildrenOfItems($categories2);
        }
        return $categories2;
    }


    public function getChildrenOfItemsForProduct($category1){
      //  global $item_cat_array2;
            foreach($category1 as $category){
                $this->item_cat_array2[]  = $category['id'];
                if (!empty($category['nested_children'])) {
                    $this->getChildrenOfItemsForProduct($category['nested_children']);    
             }  
        }
        
        return $this->item_cat_array2;
    }

    public function get_cart_item_brands($ischeckcategory = false){

        $response['ristricted'] = true;

        $applied_product_total = 0;

        $products = array_column($this->cart['cart_items'],'id');

        $affiliate_brands = $this->affiliate->brands->pluck('id')->toArray();

        if($products){

            $items = Product::with('brands')->whereIn('id',$products)->get();

            if (!empty($items)) {

                foreach ($items as $key => $value) {
                    
                     foreach ($this->cart['cart_items'] as $cart_items) {

                        $this->item_cat_array2 = array();
                        $categories = $value->categories()->with('nestedChildren')->get()->toArray();
                        $legacy_get_children_of_item  = $this->getChildrenOfItemsForProduct($categories);
                        $_check_item_existance_categories = array_intersect($legacy_get_children_of_item,$this->appliedAffiliateAssignedCategories());

                        if($this->affiliate->brands->count() > 0 && $this->affiliate->categories->count()){
                            
                            if($cart_items['id'] == $value->id && in_array($value->brands()->first()->id, $affiliate_brands) && !empty($_check_item_existance_categories)){
                                $applied_product_total += $cart_items['item_total'];  
                            }

                        }else if($this->affiliate->brands->count()  > 0){
  
                            if($cart_items['id'] == $value->id && in_array($value->brands()->first()->id, $affiliate_brands)){
                                $applied_product_total += $cart_items['item_total'];
                            }

                        }else if($this->affiliate->categories->count()  > 0){

                            if($cart_items['id'] == $value->id && !empty($_check_item_existance_categories)){
                                $applied_product_total += $cart_items['item_total'];
                            }

                        }else{
                            $response['ristricted'] = false;
                            if($cart_items['id'] == $value->id){
                                $applied_product_total += $cart_items['item_total'];
                            }
                        }
                    }
                }
            }
        }
        $response['applied_product_total'] = $applied_product_total;
        return $response;
    }

      /**
     * apply coupon 
     * @Role: API
     * @param  \Illuminate\Http\Request  $request
     * @return JSON
     */
    public function apply_affiliate(Request $request){
        $validator = Validator::make($request->all(), [
            'code' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['error'=>$validator->errors()], 401);            
        }

        $user = auth('api')->user();

        $this->cart = $user->cart_data;
 
        if(isset($this->cart['cart_items']) && empty($this->cart['cart_items'])){

        return response()->json([
                        'success' => 401,
                        'message' =>'Your cart is empty.'
                    ],401);
        }

        $affiliate = Affiliate::whereCode($request->get('code'))->first();
        
        $this->affiliate = $affiliate;

        if($affiliate){

            if($affiliate->status == 'Active'){

                    $response = $this->get_cart_item_brands();

                    if($response['ristricted'] == true && $response['applied_product_total'] == 0){

                        return response()->json([
                            'success' => 400,
                            'message' =>'This affiliation code is not valid for selected cart items.'
                        ],400);

                    }else{

                        $this->cart = $this->apply_affiliate_and_recalculate_cart($affiliate, $user, $this->cart, $response['applied_product_total']);

                        return response()->json([
                            'success' => 200,
                            'results' =>$this->cart,
                            'message' =>'Affiliation has been applied'
                        ]);                        
                    }

            }else{
                return response()->json([
                    'success' => 400,
                    'message' =>'This code seems deactived, please try again.'
                ],400);
            }
        }else{
            return response()->json([
                    'success' => 400,
                    'message' =>'Invalid affiliate code.'
            ],400);
            
        }
    }

    protected function apply_affiliate_and_recalculate_cart($affiliate, $user, $cart, $product_total = 0){
 
        if(isset($cart['meta']['coupon'])){
            unset($cart['meta']['coupon']);
        }
 
        //calculate discount
        $discount = 0;
        if($affiliate->discount > 0 ){
            if($product_total > 0){
                $discount = round(($product_total*$affiliate->discount)/100,2);
            }else{
                $discount = round(($cart['cart_subtotal']*$affiliate->discount)/100,2);
            }
        }
 
        $_calculate_total = $cart['cart_subtotal'] - $discount;
        $cart['discount'] = $discount;
        $cart['cart_total'] =  $_calculate_total;
        $cart['meta']['affiliate']['id'] = $affiliate->id;
        $cart['meta']['affiliate']['code'] = $affiliate->code;
        $cart['meta']['affiliate']['discount'] = $affiliate->discount;
        $cart['meta']['affiliate']['commission'] = $affiliate->commission;
        $user->setMeta('cart_data', $cart);
        $user->save();
        return $cart;
    }
}