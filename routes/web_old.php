<?php
use Illuminate\Support\Facades\Route;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
// clear chache route
Route::get('/clear-cache', function() {
   $exitCode    = Artisan::call('cache:clear');
   $config      = Artisan::call('config:cache');
   $view        = Artisan::call('view:clear');
   return "Cache is cleared";
});

Route::get('/', function () {
    return redirect(route('login'));
});

/*****************
* AJAX ROUTES    *
******************/
Route::match(['get'],'update_commission', 'Common\CronController@update_commission');
Route::match(['get'],'product/unhold', 'Common\CronController@unhold');
Route::match(['get'],'product/image/compress', 'Common\Temp@compressImage');
Route::match(['get'],'gallery/compressor', 'Common\Temp@compressGallery');
Route::match(['get'],'product/compress_status', 'Common\Temp@compress_status');

Route::match(['get'],'password/success', 'Auth\LoginController@password_success');

Route::match(['get','post'],'save-token', 'CommonController@saveToken');

Route::get('ajax/getSubcategories/{id}', 'CommonController@getSubcategories');
Route::get('ajax/getLevel3Categories/{id}', 'CommonController@getLevel3Categories');

Route::get('test', 'CommonController@test');
Route::post('ajax/product/saveImages', 'CommonController@saveImages');
Route::post('ajax/product/generateHtmlPartVariations', 'CommonController@generateHtmlPartVariations');
Route::post('ajax/product/deleteGalleryImage', 'CommonController@deleteGalleryImage');
Route::post('ajax/product/createSlug', 'Common\ProductController@create_unique_slug');
Route::match(['post'],'ajax/product/mark_featured', 'Common\ProductController@mark_featured');
Auth::routes();
 
/************** Custom Register start ***************/
Route::match(['get','post'],'register', 'Frontend\UserController@register');
Route::match(['get','post'],'otp-verification', 'Frontend\UserController@otp_verification');
Route::match(['get','post'],'thankyou', 'Frontend\UserController@thankyou');

/************* Custom Register start ****************/
/**********************************************
* COMMON PRODUCTS ROUTES FOR ADMIN AND VENDOR *
**********************************************/
$commonPrefixs = [
    'admin', 'vendor'
];
foreach ($commonPrefixs as $commonPrefix) {
  Route::middleware(['auth',$commonPrefix])->prefix($commonPrefix)->group(function() use ($commonPrefix){
      Route::match(['get','post'],'products', 'Common\ProductController@index');
      Route::match(['get','post'],'order/updateStatus', 'Common\OrderController@updateOrderdata')->name($commonPrefix.'.updateorder');
      Route::match(['get','post'],'product/store', 'Common\ProductController@store');
      Route::match(['get','post'],'product/edit/{id}', 'Common\ProductController@edit');
      Route::match(['get','post'],'product/delete/{id}', 'Common\ProductController@delete');
      Route::match(['get','post'],'product/view/{id}', 'Common\ProductController@view');
      Route::get('product/ajax/get', 'Common\ProductController@productList')->name('product.list');
      //Variant Options Routs Start
      Route::match(['get','post'],'variantOptions/{id}', 'Common\VariantOptionController@index');
      Route::match(['get','post'],'variantOption/store/{id}', 'Common\VariantOptionController@store');
      Route::match(['get','post'],'variantOption/edit/{id}', 'Common\VariantOptionController@edit');
      Route::match(['get','post'],'variantOption/delete/{id}', 'Common\VariantOptionController@delete');
      Route::match(['get','post'],'variantOption/add', 'Common\VariantOptionController@add_sub_variant');
      //Variant Options Routs end

      //Variants Routs Start
      Route::match(['get','post'],'variants', 'Common\VariantController@index');
      Route::match(['get','post'],'variant/store', 'Common\VariantController@store');
      Route::match(['get','post'],'variant/edit/{id}', 'Common\VariantController@edit');
      Route::match(['get','post'],'variant/delete/{id}', 'Common\VariantController@delete');

      Route::match(['get','post'],'getsalesReport', 'Common\ReportController@getsalesReport');
      
      Route::match(['get','post'],'getsalesReportCount', 'Common\ReportController@getsalesReportCount');

      Route::get('notifications', 'Auth\NotificationController@notifications')->name('notifications');
      //Variants Routs end

  }); 
}

Route::middleware(['auth'])->group(function(){
  Route::get('getNotification', 'Auth\NotificationController@index')->name('get.notification');
  Route::match(['get','post'],'order/view/{order_code}', 'Common\OrderController@order_view')->name('order.view');
  Route::match(['post'],'order/get_attributes', 'Common\OrderController@get_attributes');
  Route::match(['post'],'order/get_order_options', 'Common\OrderController@get_order_options');
});

if (request()->getHttpHost() === 'vendor.klothus.com'){
	
	Route::match(['get'],'login', 'Auth\LoginController@vendor_login')->name('login');
	Route::match(['post'],'login', 'Auth\LoginController@login');
	
}elseif (request()->getHttpHost() === 'delivery.klothus.com'){
	
	Route::match(['get'],'login', 'Auth\LoginController@deliveryguy_login')->name('login');
	Route::match(['post'],'login', 'Auth\LoginController@login');
	
}else if (request()->getHttpHost() === 'dev.klothus.com' && request()->is('api*') == false){
	return abort(401);
}

Route::match(['get','post'],'vendor/login', 'Auth\LoginController@vendor_login');
Route::match(['get','post'],'delivery/login', 'Auth\LoginController@deliveryguy_login');
Route::match(['post'],'login', 'Auth\LoginController@login');

/********************
* ADMIN ROLE ROUTES *
*********************/
//Route::group(['domain' => 'admin.klothus.com'], function () {
	Route::group(['middleware' => ['web']], function () {
		
		Route::middleware(['auth','admin'])->namespace('Admin')->prefix('admin')->group(function(){
			//Dashboard Routs Start
		  Route::get('dashboard', 'DashboardController@index')->name('admin.dashboard');
		  Route::get('menus', 'MenuController@menus')->name('admin.menus');
		  Route::post('/addcustommenu', array('as' => 'haddcustommenu', 'uses' => 'MenuController@addcustommenu'));
		  Route::post('/deleteitemmenu', array('as' => 'hdeleteitemmenu', 'uses' => 'MenuController@deleteitemmenu'));
		  Route::post('/deletemenug', array('as' => 'hdeletemenug', 'uses' => 'MenuController@deletemenug'));
		  Route::post('/createnewmenu', array('as' => 'hcreatenewmenu', 'uses' => 'MenuController@createnewmenu'));
		  Route::post('/generatemenucontrol', array('as' => 'hgeneratemenucontrol', 'uses' => 'MenuController@generatemenucontrol'));
		  Route::post('/updateitem', array('as' => 'hupdateitem', 'uses' => 'MenuController@updateitem'));

			//Dashboard Routs Start
		   //Charts Start

   		  //Roles Routs Start
		  Route::match(['get','post'],'roles', 'RoleController@index');
		  Route::match(['get','post'],'role/store', 'RoleController@store');
		  Route::match(['get','post'],'role/edit/{id}', 'RoleController@edit');
		  Route::match(['get','post'],'role/delete/{id}', 'RoleController@delete');
		  //Roles Routs end

		  //Settings Start
		  Route::match(['get','post'],'settings', 'SettingController@index');
		  //Settings end

			//Users Routs Start
		  Route::match(['get','post'],'users', 'UserController@index');
		  Route::match(['get','post'],'user/store', 'UserController@store');
		  Route::match(['get','post'],'user/edit/{id}', 'UserController@edit');
		  Route::match(['get','post'],'user/delete/{id}', 'UserController@delete');
		  Route::match(['get','post'],'role/requests', 'UserController@role_requests');
		  Route::match(['get','post'],'admin_role_request_change_status/{id}', 'UserController@admin_role_request_change_status');
		  //Users Routs end

		  //Tags Routs Start
		  Route::match(['get','post'],'tags', 'TagController@index');
		  Route::match(['get','post'],'tag/store', 'TagController@store');
		  Route::match(['get','post'],'tag/delete/{id}', 'TagController@delete');
		  //Tags Routs end

		  //Brands Routs Start
		  Route::match(['get','post'],'brands', 'BrandController@index');
		  Route::match(['get','post'],'brand/store', 'BrandController@store');
		  Route::match(['get','post'],'brand/edit/{id}', 'BrandController@edit');
		  Route::match(['get','post'],'brand/delete/{id}', 'BrandController@delete');
		  Route::match(['get','post'],'brand/getSubcategories', 'BrandController@getSubcategories');
		  //Brands Routs end

		  //Categories Routs Start
		  Route::match(['get','post'],'categories', 'CategoryController@index');
		  Route::match(['get','post'],'category/store', 'CategoryController@store');
		  Route::match(['get','post'],'category/edit/{id}', 'CategoryController@edit');
		  Route::match(['get','post'],'category/delete/{id}', 'CategoryController@delete');
		  //Categories Routs end

		  //Sub Categories Routs Start
		  Route::match(['get','post'],'subCategories', 'SubCategoryController@index');
		  Route::match(['get','post'],'subCategory/store', 'SubCategoryController@store');
		  Route::match(['get','post'],'subCategory/edit/{id}', 'SubCategoryController@edit');
		  Route::match(['get','post'],'subCategory/delete/{id}', 'SubCategoryController@delete');
		  //Sub Categories Routs end

		  //Companies Routs Start
		  Route::match(['get','post'],'vendors', 'CompanyController@index');
		  Route::match(['get','post'],'company/store', 'CompanyController@store');
		  Route::match(['get','post'],'company/edit/{id}', 'CompanyController@edit');
		  Route::match(['get','post'],'company/delete/{id}', 'CompanyController@delete');
		  Route::match(['get','post'],'vendor/sold/products/{vendor_id}', 'CompanyController@soldItems');
		  Route::match(['get','post'],'vendor/sold/product/view/{sub_order_code}', 'CompanyController@order_view');
		  Route::match(['get','post'],'vendor/verification', 'CompanyController@verification')->name('vendor.verification');
		  //Companies Routs end

		  //Customer Route Start 
		  Route::match(['get','post'],'customer', 'CustomerController@index');
		  Route::match(['get','post'],'customer/view/{id}', 'CustomerController@view');
		  Route::match(['get','post'],'customer/delete/{id}', 'CustomerController@delete');
		  Route::match(['get','post'],'customer/create/order/{id}', 'CustomerController@create_order');
		  Route::match(['get','post'],'productsFilterByKeyword', 'CustomerController@productsFilterByKeyword');
		  Route::match(['get','post'],'getOneProductDetail', 'CustomerController@getOneProductDetail');
		  Route::match(['get','post'],'getSkuValues', 'CustomerController@getSkuValues');
		  Route::match(['get','post'],'addToCart', 'CustomerController@addToCart');
		  //Cutomer Route end

		  //Orders Routs Start
		  Route::match(['get','post'],'orders/{status}', 'OrderController@index');
		  Route::match(['get','post'],'order/view/{order_code}', 'OrderController@order_view')->name('admin.order.view');
		  Route::match(['get','post'],'order/print/{order_code}', 'OrderController@print')->name('admin.order.print');
		  Route::match(['get','post'],'order/customer/print/{order_code}', 'OrderController@customerprint')->name('admin.order.customer.print');
		  Route::match(['get','post'],'order/vendor/print/{order_code}', 'OrderController@vendorprint')->name('admin.order.vendor.print');
		  Route::match(['get','post'],'adminOrderChangeStatus/{order_id}', 'OrderController@adminOrderChangeStatus');
		  Route::match(['get','post'],'asignToDeliveryGuys', 'OrderController@asignToDeliveryGuys');
		  //Orders Route end

		  //Offer Routs Start
		  Route::match(['get','post'],'offers', 'OfferController@index');
		  Route::match(['get','post'],'offer/store', 'OfferController@store');
		  Route::match(['get','post'],'offer/edit/{id}', 'OfferController@edit');
		  Route::match(['get','post'],'offer/delete/{id}', 'OfferController@delete');

		  //Banner Routs Start
		  Route::match(['get','post'],'banners', 'BannerController@index');
		  Route::match(['get','post'],'banner/store', 'BannerController@store');
		  Route::match(['get','post'],'banner/edit/{id}', 'BannerController@edit');
		  Route::match(['get','post'],'banner/delete/{id}', 'BannerController@delete');
		  Route::match(['get','post'],'banner/removeVideo/{id}', 'BannerController@removeVideo');

		  //Firebase Notifications Routs Start
		  Route::match(['get','post'],'firebase-notifications', 'FirebaseNotificationController@index');
		  Route::match(['get','post'],'firebase-notification/send', 'FirebaseNotificationController@send');
		  Route::match(['get','post'],'firebase-notification/store', 'FirebaseNotificationController@store');
		  Route::match(['get','post'],'firebase-notification/edit/{id}', 'FirebaseNotificationController@edit');
		  Route::match(['get','post'],'firebase-notification/delete/{id}', 'FirebaseNotificationController@delete');

		  Route::match(['get','post'],'reports', 'ReportController@index');
		  Route::match(['get','post'],'report/export', 'ReportController@export');

		  Route::match(['get','post'],'allreports', 'ReportController@allreports');

		  //Coupons
		  Route::match(['get','post'],'coupons', 'CouponController@index');
		  Route::match(['get','post'],'coupon/store', 'CouponController@store');
		  Route::match(['get','post'],'coupon/edit/{id}', 'CouponController@edit');
		  Route::match(['get','post'],'coupon/delete/{id}', 'CouponController@delete');
		  Route::match(['get','post'],'coupon/get_products_by_keywords', 'CouponController@get_products_by_keywords');
		  Route::match(['get','post'],'coupon/get_categories_by_keywords', 'CouponController@get_categories_by_keywords');
		  Route::match(['get','post'],'coupon/get_brands_by_keywords', 'CouponController@get_brands_by_keywords');
		  Route::match(['get','post'],'coupon/orders/{id}', 'CouponController@orders');

		  //Role Permissions Routs Start
		  Route::match(['get','post'],'rolePermissions', 'RolePermissionController@index');
		  // Route::match(['get','post'],'company/store', 'CompanyController@store');
		  // Route::match(['get','post'],'company/edit/{id}', 'CompanyController@edit');
		  // Route::match(['get','post'],'company/delete/{id}', 'CompanyController@delete');
		  //Role Permissions Routs end 

		  //Affilate Routs Start
		  Route::match(['get','post'],'affiliates', 'AffiliateController@index');
		  Route::match(['get','post'],'affiliate/store', 'AffiliateController@store');
		  Route::match(['get','post'],'affiliate/edit/{id}', 'AffiliateController@edit');
		  Route::match(['get','post'],'affiliate/delete/{id}', 'AffiliateController@delete');
		  Route::match(['get','post'],'affiliate/view/{id}', 'AffiliateController@view');
		  Route::match(['get','post'],'order_affiliate/view/{id}', 'AffiliateController@order_affiliate_update');
		  Route::match(['get','post'],'adminAffiliateChangeStatus/{id}', 'AffiliateController@update_status');
		  Route::match(['get','post'],'adminAffiliateOrderChangeStatus/{id}', 'AffiliateController@adminAffiliateOrderChangeStatus');


		/***delivery guys analytics start***/
		Route::match(['get','post'],'delivery/analytics', 'DeliveryGuysAnalyticsController@index');
		Route::match(['get','post'],'deliveryguy/reports/{user_id}', 'DeliveryGuysAnalyticsController@reports');
		Route::match(['get','post'],'deliveryguy/orders/{user_id}', 'DeliveryGuysAnalyticsController@orders');
		Route::match(['get','post'],'deliveryguy/order/view/{code}', 'DeliveryGuysAnalyticsController@order_view');
		/***delivery guys analytics end***/



		/*** customer view start***/
		Route::match(['get','post'],'customer-view', 'CustomerViewController@index');

		/*** customer view start***/

	  	//admin feedback manage
		//Route::match(['get'],'feedbacks', 'UserController@manage_feedback');
		//Route::match(['get'],'feedback/delete/{id}', 'UserController@feedback_delete');
		});
	});
//});


/********************
* VENDOR ROLE ROUTES *
*********************/
//Route::group(['domain' => 'vendor.klothus.com'], function () {
	Route::group(['middleware' => ['web']], function () {
		Route::middleware(['auth','vendor'])->namespace('Vendor')->prefix('vendor')->group(function(){
	
		  //Dashboard Tests Routs Start
		  Route::match(['get','post'],'dashboard', 'DashboardController@index')->name('vendor.dashboard');
		  //Dashboard Tests Routs Start

		  //Shop vendor profile Routs Start
		  Route::match(['get','post'],'profile', 'VendorController@profile');
		  Route::match(['get','post'],'change/password', 'VendorController@updatePassword');
		  //Shop vendor profile Routs end

		  //Orders Routs Start
		  Route::match(['get','post'],'orders/{status}', 'OrderController@index');
		  Route::match(['get','post'],'order/store', 'OrderController@store');
		  Route::match(['get','post'],'order/edit/{id}', 'OrderController@edit');
		  Route::match(['get','post'],'order/view/{vendor_id}/{sub_order_code}', 'OrderController@order_view')->name('vendor.order');
		  Route::match(['get','post'],'order/delete/{id}', 'OrderController@delete');
		  Route::match(['get','post'],'changeStatus', 'OrderController@changeStatus');
		  Route::match(['get','post'],'update_status', 'OrderController@changeStatus');
		  Route::match(['get','post'],'vendorOrderChangeStatus/{order_id}', 'OrderController@vendorOrderChangeStatus');

		  //Orders Routs end

		  //Staffs Routs Start
		  Route::match(['get','post'],'staffs', 'StaffController@index');
		  Route::match(['get','post'],'staff/store', 'StaffController@store');
		  Route::match(['get','post'],'staff/edit/{id}', 'StaffController@edit');
		  Route::match(['get','post'],'staff/delete/{id}', 'StaffController@delete');
		  //Staffs Routs end

		  // feedback route 
		   Route::match(['get','post'],'feedback-store', 'VendorController@feedback_store');

		  //Affilate Routs Start
		  Route::match(['get','post'],'affilates', 'AffilateController@index');
		  Route::match(['get','post'],'affilate/store', 'AffilateController@store');
		  Route::match(['get','post'],'affilate/edit/{id}', 'AffilateController@edit');
		  Route::match(['get','post'],'affilate/delete/{id}', 'AffilateController@delete');
		  //Affilate Routs end

		  //Stock Routs Start
		  Route::match(['get','post'],'stocks', 'StockController@index');
		  Route::match(['get','post'],'stock/store', 'StockController@store');
		  Route::match(['get','post'],'stock/edit/{id}', 'StockController@edit');
		  Route::match(['get','post'],'stock/delete/{id}', 'StockController@delete');
		  //Stock Routs end

		  //Sales Report Routs Start
		  Route::match(['get','post'],'saleReports', 'SaleReportController@index');
		  Route::match(['get','post'],'saleReport/store', 'SaleReportController@store');
		  Route::match(['get','post'],'saleReport/edit/{id}', 'SaleReportController@edit');
		  Route::match(['get','post'],'saleReport/delete/{id}', 'SaleReportController@delete');
		  //Sales Report Routs end

		  //Promo Code Offers Routs Start
		  Route::match(['get','post'],'promoCodeOffers', 'PromoCodeOfferController@index');
		  Route::match(['get','post'],'promoCodeOffer/store', 'PromoCodeOfferController@store');
		  Route::match(['get','post'],'promoCodeOffer/edit/{id}', 'PromoCodeOfferController@edit');
		  Route::match(['get','post'],'promoCodeOffer/delete/{id}', 'PromoCodeOfferController@delete');
		  //Promo Code Offers Routs end

		  Route::match(['get','post'],'reports', 'ReportController@index');
		  Route::match(['get','post'],'allreports', 'ReportController@allreports');
		  Route::match(['get','post'],'upgrade/profile', 'ProfileController@upgrade');
		});
	});
//});

/****************************
* DELIVERY GUYS ROLE ROUTES *
*****************************/
Route::middleware(['auth','delivery'])->namespace('Delivery')->prefix('delivery')->group(function(){

  //Dashboard Tests Routs Start
  Route::match(['get','post'],'dashboard', 'DashboardController@index')->name('delivery.dashboard');
  //Dashboard Tests Routs Start

  //Delivery profile Routs Start
  Route::match(['get','post'],'profile', 'DeliveryController@profile');
  //Delivery profile Routs end

  //Orders Routs Start
  Route::match(['get','post'],'orders', 'OrderController@index');
  Route::match(['get','post'],'orders/{status}', 'OrderController@index');
  // Route::match(['get','post'],'order/store', 'OrderController@store');
  // Route::match(['get','post'],'order/edit/{id}', 'OrderController@edit');
  Route::match(['get','post'],'order/view/{order_code}', 'OrderController@order_view');
  Route::match(['get','post'],'order/delete/{id}', 'OrderController@delete');
  // Route::match(['get','post'],'changeStatus', 'OrderController@changeStatus');
  Route::match(['get','post'],'deliveryOrderChangeStatus/{order_id}', 'OrderController@deliveryOrderChangeStatus');
  Route::match(['get','post'],'update_status', 'OrderController@update_status');
  Route::match(['get','post'],'upgrade/profile', 'DeliveryController@upgrade');
  //Orders Routs end
});

Route::match(['get','post'],'logout', 'Auth\LoginController@logout');
//Route::match(['get','post'],'/', 'Auth\LoginController@login');