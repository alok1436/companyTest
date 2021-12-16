<?php
namespace App\Mail\Customer;
use App\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OrderStatusChangeNotification extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public $subject = 'Your order';

    public $order = [];

    public function __construct(Order $order,$order_message,$subject)
    {
        $this->order = $order;

        $this->subject = $subject;

        $this->order_message = $order_message;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
    	$order_message = $this->order_message;

    	$subject = $this->subject;

        return $this->view('mails.apis.customer.status_change_notification',compact('order_message','subject'))->with('order',$this->order);
    }
}
