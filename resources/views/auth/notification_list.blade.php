<?php //dd($notificaitons); ?>
@foreach($notificaitons as $notificaiton) 
@if($notificaiton->type == 'order' )
<li href="javascript:void(0);" class="dropdown-item notify-item active">
  <div class="notify-icon">
    <img src="{{ isset($notificaiton->order->item->media['thumbnail']) ? url($notificaiton->order->item->media['thumbnail']) : '' }} " class="img-fluid rounded-circle" alt="" />
  </div>
  <?php $data = $notificaiton->data; ?>
  <?php if(!empty($data) && $notificaiton->order){ ?>
  	<p class="notify-details"><a href="{{ url('order/view/'.$notificaiton->order->order_code) }}?np_hash={{ $notificaiton->id }}{{ Auth::user()->hasRole('vendor') ? '&vendor_id='.$notificaiton->order->vendor_id : '' }}" >{{ $notificaiton->title }}: {{ str_limit($notificaiton->body, 40) }}</a></p>
  <?php } ?>
  <p class="text-muted mb-0 user-msg">
    <small>{{ $notificaiton->created_at->diffForHumans() }}</small>
  </p>
</li>
@elseif($notificaiton->type == 'affiliate' && Auth::user()->hasRole('admin'))
<li href="javascript:void(0);" class="dropdown-item notify-item active">
  <?php $data = $notificaiton->data; ?>
  <?php if(!empty($data)){ ?>
    <p class="notify-details"><a href="{{ url('admin/affiliate/view/'.$data['click_action']) }}?np_hash={{ $notificaiton->id }}" >{{ str_limit($data['title'], 50) }}</a></p>
  <?php } ?>
  <p class="text-muted mb-0 user-msg">
    <small>{{ $notificaiton->created_at->diffForHumans() }}</small>
  </p>
</li>
@elseif($notificaiton->type == 'new_user' && Auth::user()->hasRole('admin'))
<li href="javascript:void(0);" class="dropdown-item notify-item active">
  <?php $data = $notificaiton->data; ?>
  <?php if(!empty($data)){ ?>
    <p class="notify-details"><a href="{{ url('admin/company/edit/'.$data['click_action']) }}?np_hash={{ $notificaiton->id }}" >{{ str_limit($data['title'], 50) }}</a></p>
  <?php } ?>
  <p class="text-muted mb-0 user-msg">
    <small>{{ $notificaiton->created_at->diffForHumans() }}</small>
  </p>
</li>
@endif
@endforeach