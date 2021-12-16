<?php
namespace App;

use Cart;
use Kodeine\Metable\Metable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use Metable;

	protected $metaTable = 'users_meta'; //optional.

	public $hideMeta = false; // Do not add metas to array

	
	
    use Notifiable, HasApiTokens;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    protected $fillable = [
        'first_name', 'last_name', 'email', 'phone', 'remember_token','dob','gender','facebook_id','google_id','status','is_email_verified','mobile_device_token'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'facebook_id','google_id','password', 'remember_token','api_token','otp','status','is_email_verified','providerId','updated_at','device_token'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    //Delete the data based on user
    public static function boot() {
        parent::boot();

        static::deleting(function($user) {
             $user->metas()->delete();
             $user->roles()->delete();
        });
    }

    public function delete_role(){
        return $this->hasOne(UserRole::class);
    }

    public function roles()
    {
      return $this->belongsToMany(Role::class);
    }
   
    public function mainOrders(){
      return $this->hasMany('App\MainOrder','customer_id');
    }

    public function orders(){
      return $this->hasMany('App\Order','customer_id');
    }

    public function holdItems(){
      return $this->hasMany('App\HoldProduct','customer_id');
    }

    public function shopOrders(){
      return $this->hasMany('App\Order','vendor_id');
    }   
	
    public function deliveryGuyOrders(){
        return $this->belongsToMany('App\Order');
    }
    
	public function products(){
		return $this->hasMany('App\Product','vendor_id');
    }

    public function affiliations(){
        return $this->hasMany('App\Affiliate');
    }

    public function deviceToken(){
        return $this->hasMany('App\DeviceToken');
    }

    public function activeSessions(){
        return $this->hasMany('App\Session');
    }

    public function getCurrentActiveUserDeviceTokens(){
        $tokens = $this->activeSessions()->groupBy('device_token')->get()->pluck('device_token')->toArray();
        return !empty($tokens) ? $tokens : [];
    }  	
    public function notifications(){
        return $this->hasMany('App\Notification');
    }
    /**
    * @param string|array $roles
    */
    public function authorizeRoles($roles)
    {
      if (is_array($roles)) {
          return $this->hasAnyRole($roles) || 
          abort(401, 'This action is unauthorized.');
      }
      return $this->hasRole($roles) || 
          abort(401, 'This action is unauthorized.');
    }
    /**
    * Check multiple roles
    * @param array $roles
    */
    public function hasAnyRole($roles)
    {
      return null !== $this->roles()->whereIn('name', $roles)->first();
    }
    /**
    * Check one role
    * @param string $role
    */
    public function hasRole($role)
    {
      return null !== $this->roles()->where('name', $role)->first();
    }
    
    /**
     * Gather the full name
     *
     * @return array
     */
    public function getFullNameAttribute() {
        return ucfirst($this->first_name) . ' ' . ucfirst($this->last_name);
    }
    
    /**
     * Gather the with customer name with phone  
     *
     * @return array
     */
    public function getCustomerNameAttribute() {
        return ucfirst($this->first_name) . ' ' . ucfirst($this->last_name). ($this->phone ? ' ('.$this->phone.')' : '');
    }

    /**
     * Gather the data for the product in the sequence manner.
     *
     * @return array
     */
    public function prepareCart(){
        return [
            'cart_items' => Cart::session($this->id)->getContent(),
            'cart_total_qunatity' =>Cart::session($this->id)->getTotalQuantity(),
            'cart_subtotal' =>Cart::session($this->id)->getSubTotal(),
            'cart_total' =>Cart::session($this->id)->getSubTotal(),
        ];
    }

    public function categories(){
        return $this->belongsToMany('App\Category');
    }
    /**
     * Gather the data for the product in the sequence manner.
     *
     * @return array
     */
    public function custom_meta_data(){
      $data = [];
      foreach ($this->getMeta()->toArray() as $key => $value) {
            if(in_array($key, ['certficate_images','banners','company_logo'])){
                if (is_array($value)) {
                   foreach ($value as $key2 => $value2) {
                       if (!is_array($value2)) {
                            $data[$key][$key2] = $value2 ? url($value2) : '';
                       }
                    } 
                }else{
                    $data[$key] = $value ? url($value) : '';
                }
            }else{
                $data[$key] = $value;
            }
      } 
      return $data;
    }
    /**
     * Convert the model instance to an array.
     *
     * @return array
     */
    public function toArray()
    {
        return $this->hideMeta ?
            parent::toArray() :
            array_merge(parent::toArray(), [
                'meta_data' => $this->custom_meta_data(),
            ]);
    }
}
