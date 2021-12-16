@extends(Auth::user()->roles()->first()->name.'.layouts.default') 
@section('pageTitle', Auth::user()->name.' | Notifications') 
@section('content')
<div class="container-fluid">
  <div class="row">
    <div class="col-12">
      <div class="card-box">
        <div class="col-9">
          <h3>Notifictions</h3>
        </div>
    </div>
    </div>
  </div>
<div class="row">
    <div class="col-md-12">
    <ul style="padding: 0">
@if($notificaitons->count() >0)
@foreach($notificaitons as $notificaiton)
@if($notificaiton->type == 'order' )
    <li class="dropdown-item notify-item active list">
      <div class="row">
        <div class="col-md-1">
          <div class="notify-icon">
            <img src="{{ isset($notificaiton->order->item->media['thumbnail']) ? url($notificaiton->order->item->media['thumbnail']) : '' }} " class="img-fluid rounded-circle" alt="" width="50px" />
          </div>
        </div>
        <div class="col-md-11">
        <?php $data = $notificaiton->data; ?>
        <?php if(!empty($data) && $notificaiton->order){ ?>
        	<p class="notify-details"><a href="{{ url('order/view/'.$notificaiton->order->order_code) }}?np_hash={{ $notificaiton->id }}{{ Auth::user()->roles()->first()->name == 'vendor' ? '&vendor_id='.$notificaiton->order->vendor_id : '' }}" >{{ $notificaiton->title }}: {{ $notificaiton->body }}</a></p>
        <?php } ?>
        <p class="text-muted mb-0 user-msg">
          <small>{{ $notificaiton->created_at->diffForHumans() }}</small>
        </p>
      </div>
    </div>
    </li>
    @elseif($notificaiton->type == 'affiliate' && Auth::user()->hasRole('admin'))
    <li class="dropdown-item notify-item active list">
      <div class="row">
        <div class="col-md-1">
          <div class="notify-icon">    
          </div>
        </div>
        <div class="col-md-11">
        <?php $data = $notificaiton->data; ?>
        <p class="notify-details"><a href="{{ url('admin/affiliate/view/'.$data['click_action']) }}?np_hash={{ $notificaiton->id }}" >{{ str_limit($data['title'], 50) }}</a></p>
        <p class="text-muted mb-0 user-msg">
          <small>{{ $notificaiton->created_at->diffForHumans() }}</small>
        </p>
      </div>
    </div>
    </li>
    @elseif($notificaiton->type == 'new_user' && Auth::user()->hasRole('admin'))
    <li class="dropdown-item notify-item active list">
      <div class="row">
        <div class="col-md-1">
          <div class="notify-icon">
             
          </div>
        </div>
        <div class="col-md-11">
        <?php $data = $notificaiton->data; ?>
        <p class="notify-details"><a href="{{ url('admin/company/edit/'.$data['click_action']) }}?np_hash={{ $notificaiton->id }}" >{{ $notificaiton->title }}</a></p>
        <p class="text-muted mb-0 user-msg">
          <small>{{ $notificaiton->created_at->diffForHumans() }}</small>
        </p>
      </div>
    </div>
    </li>
    @endif
    @endforeach
    @else

    <li class="dropdown-item notify-item active">
      No notification found
    </li>

    @endif
    </ul>
  </div>
</div>
</div>
@endsection
