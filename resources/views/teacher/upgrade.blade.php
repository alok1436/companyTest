@extends('delivery.layouts.default') 
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
            {{ Form::open(array('url' => 'delivery/upgrade/profile', 'files' => true, 'class' => '','id'=>'form', 'enctype'=>'multipart/form-data')) }}
              <div class="form-row">
                <div class="form-group col-md-6">
                  <label class="col-form-label">Select Role</label>
                  <select name="role" class="form-control select_role">
                      <option value="customer" {{ Auth::user()->hasRole('customer') ? 'disabled' : '' }}>Customer</option>
                      <option value="vendor" {{ Auth::user()->hasRole('vendor') ? 'disabled' : '' }}>Vendor</option>
                  </select>
                </div>
              </div>

              <div class="vendor_wr" style="display: none">
                <div class="form-row">
                  <div class="form-group col-md-6">
                    <label class="col-form-label">First Name</label>
                    {{Form::text('first_name',$user->first_name,array('class'=>'form-control','required'=>true)) }}
                  </div>
                  <div class="form-group col-md-6">
                    <label class="col-form-label">Last Name</label>
                    {{Form::text('last_name',$user->last_name,array('class'=>'form-control','required'=>true)) }}
                  </div>
              </div>

              <div class="form-row">
                <div class="form-group col-md-6">
                  <label class="col-form-label">Registration Number</label>
                  {{Form::text('registration_number',$user->getMeta('registration_number'),array('class'=>'form-control','required'=>true)) }}
                </div>
                <div class="form-group col-md-6">
                  <label class="col-form-label">Certficates Images 3/4</label>
                  {{Form::file('certficate_images',array('class'=>'form-control') ) }}
                  @if($user->getMeta('certficate_images') && file_exists(asset($user->getMeta('certficate_images'))))
                  <a class="example-image-link" href="{{ asset($user->getMeta('certficate_images')) }}" data-lightbox="example-set">
                    <img width="35" height="35" src="{{ asset($user->getMeta('certficate_images')) }}" class="rounded-circle">
                  </a>
                  @endif
                </div>
              </div>

              <div class="form-row">
                <div class="form-group col-md-6">
                  <label class="col-form-label">Registered Company Name</label>
                  {{Form::text('reg_company_name',$user->getMeta('reg_company_name'),array('class'=>'form-control','required'=>true) ) }}
                </div>
                <div class="form-group col-md-6">
                  <label class="col-form-label">Location</label>
                  {{Form::text('location',$user->getMeta('location'),array('class'=>'form-control','required'=>true)) }}
                </div>
              </div>

              <div class="form-row">
                <div class="form-group col-md-6">
                  <label class="col-form-label">Register Personal</label>
                  {{Form::text('register_personal',$user->getMeta('register_personal'),array('class'=>'form-control','required'=>true) ) }}
                </div>
                <div class="form-group col-md-6">
                  <label class="col-form-label">Representative</label>
                  {{Form::text('representative',$user->getMeta('representative'),array('class'=>'form-control','required'=>true)) }}
                </div>
              </div>

              <div class="form-row">
                <div class="form-group col-md-6">
                  <label class="col-form-label">Business Name</label>
                  {{Form::text('business_name',$user->getMeta('business_name'),array('class'=>'form-control','required'=>true)) }}
                </div>
                <div class="form-group col-md-6">
                  <label class="col-form-label">Business Type</label>
                  {{Form::text('business_type',$user->getMeta('business_type'),array('class'=>'form-control','required'=>true)) }}
                </div>
              </div>

              <div class="form-row">
                <div class="form-group col-md-6">
                  <label class="col-form-label">Shop Open Date</label>
                  {{Form::date('shop_open_date',$user->getMeta('shop_open_date'),array('class'=>'form-control','required'=>true,'max'=>date('Y-m-d')) ) }}
                </div>

                <div class="form-group col-md-6">
                  <label class="col-form-label">Banners</label>
                  {{Form::file('banners',array('class'=>'form-control','multiple'=>'multiple')) }}
                  @if($user->getMeta('banners') && file_exists(asset($user->getMeta('banners'))))
                  <a class="example-image-link" href="{{ asset($user->getMeta('banners')) }}" data-lightbox="example-set">
                    <img width="35" height="35" src="{{ asset($user->getMeta('banners')) }}" class="rounded-circle">
                  </a>
                  @endif
                </div>
              </div>

              <div class="form-row">
                <div class="form-group col-md-6">
                  <label class="col-form-label">Company Logo Upload</label>
                  {{Form::file('company_logo',array('class'=>'form-control') ) }}
                  @if($user->getMeta('company_logo') && file_exists(asset($user->getMeta('company_logo'))))
                  <a class="example-image-link" href="{{ asset($user->getMeta('company_logo')) }}" data-lightbox="example-set">
                    <img width="35" height="35" src="{{ asset($user->getMeta('company_logo')) }}" class="rounded-circle">
                  </a>
                  @endif
                </div>
                <div class="form-group col-md-6">
                  <label class="col-form-label">Email</label>
                  {{Form::email('email',$user->email,array('class'=>'form-control','required'=>true)) }}
                </div>
              </div>

              <div class="form-row">
                <div class="form-group col-md-6">
                  <label class="col-form-label">Password</label>
                  {{Form::password('password',array('class'=>'form-control')) }}
                  <em>Leave blank if you don't want to change</em>
                </div>
                <div class="form-group col-md-6">
                  <label class="col-form-label">Phone</label>
                  {{Form::text('phone',$user->phone,array('class'=>'form-control','required'=>true) ) }}
                </div>
              </div>

              <div class="form-row">
                <div class="form-group col-md-6">
                  <label class="col-form-label">Date of Birth</label>
                  {{Form::date('dob',$user->getMeta('dob'),array('class'=>'form-control')) }}
                </div>
                <div class="form-group col-md-6">
                  <label class="col-form-label">Gender</label>
                  {{Form::select('gender',['Male'=>'Male','Female'=>'Female'],$user->getMeta('gender'),array('class'=>'form-control','required'=>true) ) }}
                </div>
              </div>

              <div class="form-row">
                <div class="form-group col-md-6">
                  <label class="col-form-label">Commission(%)</label>
                  {{Form::text('commission',$user->commission,array('class'=>'form-control')) }}
                </div>
                <!-- <div class="form-group col-md-3">
                  <label class="col-form-label">Email Verfied</label>
                  {{Form::select('is_email_verified',['Yes'=>'Yes','No'=>'No'],$user->is_email_verified,array('class'=>'form-control','required'=>true)) }}
                </div> -->
              </div>

              <div class="form-row">
                <div class="form-group col-md-12">
                  <label class="col-form-label">Address</label>
                  {{Form::textarea('address',$user->getMeta('address'),array('class'=>'form-control','required'=>true)) }}
                </div>
              </div>

              </div>

              <button type="submit" class="btn btn-primary">SUBMIT</button>
            {{ Form::close() }}
        </div>
    </div>
  </div>
</div>
@endsection