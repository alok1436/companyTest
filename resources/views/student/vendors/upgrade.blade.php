@extends('vendor.layouts.default') 
@section('pageTitle', 'Vendor | Upgrade profile') 
@section('content')

<div class="container-fluid">
  <div class="row">
    <div class="col-12">
      <div class="card-box">
        <?php $requests = \App\RoleRequest::where(['user_id'=>Auth::id(),'status'=>'Pending'])->get(); ?>
        @if($requests)
          @foreach($requests as $r)
            <div class="alert alert-danger">
              <strong></strong> We're reviewing your request for the {{ $r->role->description }} role, you will be get notified soon.
            </div>
          @endforeach
        @endif

        <?php $requests = \App\RoleRequest::where(['user_id'=>Auth::id(),'status'=>'Approved'])->get(); ?>
        @if($requests)
          @foreach($requests as $r)
            <div class="alert alert-success">
              Your request has been approved, Now you became {{ $r->role->description }}
            </div>
          @endforeach
        @endif
    </div>
    </div>
  </div>
  <div class="row">
    <div class="col-md-12">
        <div class="card-box">
            <h4 class="m-t-0 header-title">Upgrade Profile</h4>
            {{ Form::open(array('url' => 'vendor/upgrade/profile', 'files' => true, 'class' => '','id'=>'form', 'enctype'=>'multipart/form-data')) }}
              <div class="form-row">
                <div class="form-group col-md-6">
                  <label class="col-form-label">Select Role</label>
                  <select name="role" class="form-control">
                      <option value="customer" {{ Auth::user()->hasRole('customer') ? 'disabled' : '' }}>Customer</option>
                      <option value="delivery" {{ Auth::user()->hasRole('delivery') ? 'disabled' : '' }}>Delivery guy</option>
                  </select>
                </div>
              </div>
              <button type="submit" class="btn btn-primary">SUBMIT</button>
            {{ Form::close() }}
        </div>
    </div>
  </div>
</div>
@endsection