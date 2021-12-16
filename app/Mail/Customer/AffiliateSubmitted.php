<?php
namespace App\Mail\Customer;

use App\User;
use App\Affiliate;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AffiliateSubmitted extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public $subject = null;
 
    public $affiliate = null;

    public function __construct(Affiliate $affiliate)
    {
        $this->affiliate = $affiliate;
        $this->subject = 'Affiliate submitted (Code:'.$this->affiliate->code.')';
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $affiliate = $this->affiliate;

        return $this->view('mails.apis.customer.affiliate_submitted',compact('affiliate'));
    }
}
