<?php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group(['middleware' => 'auth:api','prefix' => 'v1'], function(){

	Route::get('user',function(Request $request){
		return $request->user();
	});

	Route::post('update_device_token', 'API\UserController@update_device_token');
	Route::get('users', 'API\UserController@getUsers');
	
    Route::get('cart', 'API\CartApiController@cart');

	//carts routes
	Route::post('cart/store', 'API\CartApiController@store');	
	Route::post('cart/remove', 'API\CartApiController@remove');
	Route::post('cart/apply_coupon', 'API\CartApiController@apply_coupon');
	Route::post('cart/apply_affiliate', 'API\CartApiController@apply_affiliate');
	Route::get('checkout/hold', 'API\CartApiController@holdProduct');
	Route::get('checkout/unhold', 'API\CartApiController@unhold');
	Route::get('checkout/check', 'API\CartApiController@check_holded_product_while_checkout');

	//wishlist routes
	Route::post('wishlist/store', 'API\WishlistApiController@store');
	Route::post('wishlist/remove', 'API\WishlistApiController@remove');
	Route::get('wishlist/get', 'API\WishlistApiController@get');
	
	//wishlist routes
	Route::post('order/create', 'API\OrderApiController@create');
	Route::get('order/get', 'API\OrderApiController@get');
	Route::post('order/update', 'API\OrderApiController@update_order_status');
	Route::post('mainorder/update', 'API\OrderApiController@mainorder_update');

	//customer profile edit
	Route::get('customer/edit', 'API\CustomerApiController@edit'); 
    Route::match(['GET','post'],'customer/update', 'API\CustomerApiController@update');
    Route::match(['post'],'assignRole', 'API\CustomerApiController@assignRole');
    
    // vendor profile edit
    Route::get('vendor/edit', 'API\VendorApiController@edit');
    Route::post('vendor/update', 'API\VendorApiController@update');
    Route::get('vendor/analytics', 'API\VendorApiController@analytics');
	Route::post('vendor/orders', 'API\VendorApiController@orders');
	Route::get('vendor/order/{subordercode}', 'API\VendorApiController@get');
    Route::post('vendor/order/update_status', 'API\VendorApiController@update_status');
	
	//change password
	Route::post('updatePassword', 'API\UserController@updatePassword');
	Route::post('logout', 'API\UserController@logout');

	//review store
	Route::post('review/store', 'API\ReviewApiController@store');
	Route::post('order/action/cancel', 'API\OrderApiController@cancel_order');
	Route::get('review/get', 'API\ProductApiController@product_review_get');
	Route::match(['post','get'],'notification/create', 'API\NotificationApiController@create');

	//Delivery guys routes start
    Route::post('deliveryguy/orders/get', 'API\DeliveryGuysApiController@orders');	
    Route::post('deliveryguyChangeOrderStatus', 'API\DeliveryGuysApiController@update_status');	
    Route::get('deliveryguy/analytics', 'API\DeliveryGuysApiController@analytics');
    Route::get('deliveryguy/order/{subordercode}', 'API\DeliveryGuysApiController@get');

    //change gmail
    Route::post('change_email_or_phone_request', 'API\UserController@change_email_or_phone_request');
    Route::post('update_phone_or_email', 'API\UserController@update_phone_or_email');
    Route::get('get_affiliations', 'API\CustomerApiController@get_affiliations');
    Route::post('affiliation/create/request', 'API\CustomerApiController@create_affiliate_request');
    Route::post('create_affiliate_request', 'API\CustomerApiController@create_affiliate_request');
	Route::post('customer/update', 'API\CustomerApiController@update');

	//notification routes
	Route::get('notification/get', 'API\NotificationApiController@get');
	Route::post('notification/read', 'API\NotificationApiController@read');
	Route::post('notification/remove', 'API\NotificationApiController@remove');
});
	
Route::group([
	'middleware' => ['api','cors'],
	'prefix' => 'v1',
], function ($router) {
	

	Route::match(['post'],'authentication', 'API\AuthenticationController@social_login');
    Route::match(['post'],'login', 'API\UserController@login');
	Route::match(['post'],'loginWithOtp', 'API\UserController@logInVerifyOtp');
	Route::post('register', 'API\UserController@register');
	Route::post('forgetPassword', 'API\UserController@send_message_to_update_password');
	
	//vendor routes
	Route::post('vendor/registration', 'API\VendorApiController@VerifyNregisterVendor')->middleware(['throttle:20,1']);
	Route::post('vendor/verify_otp', 'API\VendorApiController@verify_otp')->middleware(['throttle:20,1']);
	Route::post('resend_otp', 'API\UserController@resend_otp')->middleware(['throttle:20,1']);
	Route::get('vendor/products/{id}', 'API\VendorApiController@getProductByVendorId');
	Route::post('vendor/update', 'API\VendorApiController@update');
	Route::post('vendor/inquiry', 'API\VendorApiController@inquiry');
    Route::get('vendor/product/{id}', 'API\ProductApiController@get_product_by_vendor');
    
	//get products routes
	Route::get('products', 'API\ProductApiController@get_products');
	Route::get('get_recent_products', 'API\ProductApiController@get_recent_products');
	Route::get('top_products', 'API\ProductApiController@top_products');
	Route::get('get_products_for_home_page','API\ProductApiController@get_products_for_home_page');
	Route::get('product/{params}', 'API\ProductApiController@get');
	Route::get('getproductByCategoryId', 'API\ProductApiController@getproductByCategoryId');

    
	//get brands routes
	Route::get('brands', 'API\BrandApiController@get_brands');

	//category routes
	Route::get('category/{params1}', 'API\CategoryApiController@get_products_by_category');
	Route::get('category/{params1}/{params2}', 'API\CategoryApiController@get_products_by_category');
	Route::get('category/{params1}/{params2}/{params3}', 'API\CategoryApiController@get_products_by_category');
	Route::get('categories', 'API\CategoryApiController@get_all_categories');
	Route::get('parentCategories', 'API\CategoryApiController@get_parent_categories');
	Route::get('get_child_categories/{parent_id}','API\CategoryApiController@get_parent_with_child_categories');
	//Tags routes

	Route::get('tag/{params}','API\TagApiController@get_products_by_tag');

	//Tags routes
	Route::get('brand/{params}','API\BrandApiController@get_products_by_brand');

	//Search routes
	Route::match(['get','post'],'shop', 'API\ShopApiController@index');

	Route::match(['get','post'],'menus/{id}', 'API\MenuApiController@index');
	
	//order routes
	Route::post('changeOrderStatus/{id}', 'API\OrderApiController@changeOrderStatus');
	Route::post('change_all_order_status/{order_code}', 'API\OrderApiController@change_all_order_status');

	//customer routes
	Route::match(['post'],'customer/register', 'API\CustomerApiController@registration')->middleware(['throttle:20,1']);
	Route::post('customer/verify_otp', 'API\CustomerApiController@verify_otp')->middleware(['throttle:20,1']);
	Route::match(['get'],'customer/order/{id}', 'API\CustomerApiController@customer_order_by_id'); 

	//search  routes
	Route::get('search', 'API\SearchApiController@search');
	Route::get('notification/send', 'API\NotificationApiController@send');
	// socialite login
	
    Route::post('check/account', 'API\UserController@user_verification_while_new_registration')->middleware(['throttle:20,1']);
    Route::post('account/verification', 'API\UserController@user_verification_verifyotp_complete_registration')->middleware(['throttle:20,1']);
    Route::get('login/redirect/{provider}', 'API\UserController@redirect');
    Route::get('login/{provider}/callback', 'API\UserController@handleCallback');
    Route::post('change_password_using_fp', 'API\UserController@change_password_using_fp');

    Route::get('offer/get', 'API\OfferApiController@get');
    Route::get('banner/get', 'API\BannerApiController@get');
});