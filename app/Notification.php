<?php

namespace App;
use Auth;
use FCM;
use LaravelFCM\Message\OptionsBuilder;
use Illuminate\Database\Eloquent\Model;
use LaravelFCM\Message\PayloadDataBuilder;
use Illuminate\Database\Eloquent\SoftDeletes;
use LaravelFCM\Message\PayloadNotificationBuilder;

class Notification extends Model
{
	use SoftDeletes;
	protected $fillable = ['user_id', 'order_id'];
	protected $dates = ['deleted_date'];
    protected $with = ['order'];

	public function order(){
        return $this->hasOne('App\Order','id','order_id');
    }
    /**
     * Gather the full name
     *
     * @return array
     */
    public function getDataAttribute() {
        return json_decode($this->attributes['data'],TRUE);
    }

    public function sendPush($target, $title, $message, $image, $data = array()){

        $url = 'https://fcm.googleapis.com/fcm/send';
        $fields = array();
        $fields['priority'] = "high";
        $fields['notification'] = ['title' => $title, 'body' => $message,'image' => $image];
        $fields['data'] = $data; 
        $fields['data']['click_action'] = 'FLUTTER_NOTIFICATION_CLICK'; 

        if (is_array($target)){
            $fields['registration_ids'] = $target;
        } else{
            $fields['to'] = $target;
        }

        $headers = array(
        'Content-Type:application/json',
        'Authorization:key=AAAA2y_cGVw:APA91bEvVpXu4ncVzuunPoQun62sjplH_FQzbMJscspSiDffPQxkB7EWw81rjYf8es0gECHXatLoMZcjIrvXBi2G4XUzinsi_osFG0PmftVtBzgC0K_jxGojRDBajr_EeB1ZeVvR8Nwr'
        );
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
        $result = curl_exec($ch);

        if ($result === FALSE) {
            die('FCM Send Error: ' . curl_error($ch));
        }
        curl_close($ch);
        return json_decode($result);
    }

    public function toSingleDevice($registrationIds=null, $title=null, $body=null, $icon, $click_action,$userId = 0){
     
        $SERVER_API_KEY = 'AAAA2y_cGVw:APA91bEvVpXu4ncVzuunPoQun62sjplH_FQzbMJscspSiDffPQxkB7EWw81rjYf8es0gECHXatLoMZcjIrvXBi2G4XUzinsi_osFG0PmftVtBzgC0K_jxGojRDBajr_EeB1ZeVvR8Nwr';
     
        $header = [
        'Authorization: Key=' . $SERVER_API_KEY,
        'Content-Type: Application/json'
        ];

        $msg = [
            'title' => $title,
            'body' => $body,
            'click_action' => $click_action,
            'icon' => $icon,
            'image' =>$icon,
            'badge' => $this->scopeNumberAlertForUser($userId) 
        ];
        if (is_array($registrationIds)){
            $payload['registration_ids'] = $registrationIds;
        } else{
            $payload['to'] = $registrationIds;
        }
        $payload['data'] = $msg;
        $payload['priority'] = 'high';

        
        $curl = curl_init();

        curl_setopt_array($curl, array(
          CURLOPT_URL => "https://fcm.googleapis.com/fcm/send",
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_CUSTOMREQUEST => "POST",
          CURLOPT_POSTFIELDS => json_encode( $payload ),
          CURLOPT_HTTPHEADER => $header
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
          //echo "cURL Error #:" . $err;
        	return array();
        } else {
          return array('response'=>json_decode($response),'payload'=>$payload);
        }
    }

    public function toMultiDevice($modal, $token=null, $title=null, $body=null, $icon, $click_action){
		$optionBuilder = new OptionsBuilder();
		$optionBuilder->setTimeToLive(60*20);

		$notificationBuilder = new PayloadNotificationBuilder($title);
		$notificationBuilder->setBody($body)
						    ->setSound('default')
						    ->setBadge($this->where('read_at',null)->count())
						    ->setClickAction($click_action)
						    ->setIcon($icon);

		$dataBuilder = new PayloadDataBuilder();
		$option = $optionBuilder->build();
		$notification = $notificationBuilder->build();
		$data = $dataBuilder->build();
		$tokens = $modal::pluck('device_token')->toArray();

		$downstreamResponse = FCM::sendTo($tokens, $option, $notification, $data);

		$downstreamResponse->numberSuccess();
		$downstreamResponse->numberFailure();
		$downstreamResponse->numberModification();
		$downstreamResponse->tokensToDelete();
		$downstreamResponse->tokensToModify();
		$downstreamResponse->tokensToRetry();
		$downstreamResponse->tokensWithError();   	
    }
    
	public function read(){
        return $this->where(['read_at'=>null,'user_id'=>Auth::id()])->orderBy('id','desc')->get();
    }

    public function scopeNumberAlert(){
        return $this->where(['read_at'=>null,'user_id'=>Auth::id()])->count();	
    }

    public function scopeNumberAlertForUser($userId){
        return $this->where(['read_at'=>null,'user_id'=>$userId])->count();
    }

    public static function boot() {



	    parent::boot();



	    static::created(function($item) {

	        

	    });



	    static::updated(function($item) {

	        //Event::fire('item.updated', $item);

	    });



	    static::deleted(function($item) {

	        //Event::fire('item.deleted', $item);

	    });

	}
}
