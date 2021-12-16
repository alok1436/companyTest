Hi {{ $affiliate->customer->full_name }},

<p>Thank you for being with Klothus</p>

@if($affiliate->status == 'Active')

<p>Your affilication has been Activated, Thank you </p>

@else

<p>Your affilication has been deactivated, We will get back to you soon, Thank you </p>

@endif

<br>
<br>
<p>Customer Care : 014813643</p>
