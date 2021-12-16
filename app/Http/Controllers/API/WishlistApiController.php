<?php
namespace App\Http\Controllers\API;
use Cart;
use Session;
use App\Role;
use App\User;
use App\Order;
use Validator;
use App\Company;
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

class WishlistApiController extends Controller
{
    public $errors =[];

    public $wishlist =[];

    /**
     * Vendor store wishlist
     * @Role: API
     * @param  \Illuminate\Http\Request  $request
     * @return JSON
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'items' => 'required',
        ]);
        
        if ($validator->fails()) {
            return response()->json(['error'=>$validator->errors()], 401);            
        }
         
        $user = auth('api')->user();
        if($user){
            foreach ($request->get('items') as $key => $wishlist_item) {
                $product = Product::find($wishlist_item['id']);
                if($product){

                    if(!empty($user->getMeta('wishlist_data')['wishlist_items'])){
                           foreach ($user->getMeta('wishlist_data')['wishlist_items'] as $value) {
                                            $id[] = $value['id'];
                           }
                    }else{
                        $id = array();
                    }
                   if(!in_array($wishlist_item['id'],$id)){
                        $this->wishlist['wishlist_items'][] = [
                                'id' => $product->id,
                                'name' => $product->name,
                                'price' => $wishlist_item['price'],
                                'quantity' => $wishlist_item['quantity'],
                                'attributes' => $wishlist_item['attributes'],
                                'item_data' => [
                                    'slug'=>$product->slug,
                                    'attatchments'=>$product->custom_meta_data()
                                ]
                            ];
                     }else{
                         return response()->json([
                                    'success' => 400,
                                    'message' =>'already added wishlist.'
                            ],400);
                     }
                }else{
                    $this->errors[] = 'Requested item '.$wishlist_item['name'].' is Invalid';
                }
             
            }
            
            $alreadyWishlisted =  $user->getMeta('wishlist_data');
            if(!empty($alreadyWishlisted)){
                $this->wishlist['wishlist_items'] = array_merge($this->wishlist['wishlist_items'],$alreadyWishlisted['wishlist_items']);
            }
            $user->setMeta('wishlist_data',$this->wishlist);
            $user->save();       
            return response()->json([
                'success' => true,
                'results' =>$this->wishlist,
                'warnings'=>$this->errors,
                'message' =>'Wishlist has been added.'
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
    public function get(Request $request)
    {
        $user = auth('api')->user();
        if($user){
            return response()->json([
                'success' => true,
                'results' =>$user->getMeta('wishlist_data'),
                'message' =>'Wishlist has been loaded.'
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
                'item_id' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['error'=>$validator->errors()], 401);            
        }

        if($user){
            $cartData = $user->getMeta('wishlist_data');
            if(!empty($cartData)){
             foreach ($cartData['wishlist_items'] as $key=>$value) {

                if ($request->get('item_id') == $value['id']) {
                    unset($cartData[$key]);
                }else{
                    $this->wishlist['wishlist_items'][] = $value;  
                }  
            }

            $user->setMeta('wishlist_data',$this->wishlist);
            $user->save(); 

            return response()->json([
                'success' => 200,
                'results' =>$this->wishlist,
                'message' =>'Item has been removed.'
            ]);

            }else{
                return response()->json([
                    'success' => 400,
                    'message' =>'Your wishlist is empty.'
                ],400); 
             }
            }else{
                return response()->json([
                    'success' => 400,
                    'message' =>'Invalid customer.'
            ],400);
        } 
    }

}