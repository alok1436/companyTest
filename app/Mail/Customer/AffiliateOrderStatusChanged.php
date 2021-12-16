<?php
namespace App\Mail\Customer;

use App\User;
use App\AffiliateOrder;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AffiliateOrderStatusChanged extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public $subject = null;
 
    public $affiliateOrder = null;

    public function __construct(AffiliateOrder $affiliateOrder)
    {
        $this->affiliateOrder = $affiliateOrder;

        $this->subject = 'Affiliation commission notification (Code:'.$this->affiliateOrder->affiliate->code.')';
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $affiliateOrder = $this->affiliateOrder;

        return $this->view('mails.apis.customer.affiliate_order_status_update',compact('affiliateOrder'));
    }
}
