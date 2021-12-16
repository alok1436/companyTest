@extends('vendor.layouts.default') 
@section('pageTitle', 'Vendor | Edit Profile') 
@section('content')

<div class="container-fluid">
  <div class="row">
    <div class="col-md-12">
        <div class="card-box">
            <h4 class="m-t-0 header-title">Vendor Profile</h4>
            {{ Form::open(array('url' => 'vendor/profile', 'files' => true, 'class' => '','id'=>'form', 'enctype'=>'multipart/form-data')) }}
              <div class="form-row">
                <div class="form-group col-md-6">
                  <label class="col-form-label">Registration Number</label>
                  {{Form::text('registration_number',$user->getMeta('registration_number'),array('class'=>'form-control','required'=>true)) }}
                </div>
                <div class="form-group col-md-6">
                  <label class="col-form-label">Certficates Images 3/4</label>
                  {{Form::file('certficate_images',array('class'=>'form-control') ) }}
                  @if($user->getMeta('certficate_images'))
                    <a class="example-image-link" href="{{ asset($user->getMeta('certficate_images')) }}" data-lightbox="example-set">
                      <img width="35" height="35" src="{{ asset($user->getMeta('certficate_images')) }}" class="rounded-circle">
                    </a>
                  @endif
                </div>
              </div>

              <div class="form-row">
                <div class="form-group col-md-6">
                  <label class="col-form-label">Registered Company Name</label>
                  {{Form::text('name',$user->first_name,array('class'=>'form-control','required'=>true) ) }}
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
                  <label class="col-form-label">Select Brands</label>
                  {{Form::select('brand_id[]',$brands,$user->getMeta('brand_id'),array('class'=>'form-control select2','multiple'=>'multiple')) }}
                </div>
              </div>

              <div class="form-row">
                <div class="form-group col-md-6">
                  <label class="col-form-label">Select Category</label>
                  {{Form::select('categiry_id[]',$categories,$user->categories,array('class'=>'form-control select2','multiple'=>'multiple') ) }}
                </div>
                <div class="form-group col-md-6">
                  <label class="col-form-label">Banners</label>
                  {{Form::file('banners',array('class'=>'form-control','multiple'=>'multiple')) }}
                  @if($user->getMeta('banners'))
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
                  @if($user->getMeta('company_logo'))
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
                <!--div class="form-group col-md-3">
                  <label class="col-form-label">Status</label>
                  {{Form::select('status',['Active'=>'Active','Inactive'=>'Inactive'],$user->status,array('class'=>'form-control','required'=>true)) }}
                </div>
                <div class="form-group col-md-3">
                  <label class="col-form-label">Email Verfied</label>
                  {{Form::select('is_email_verified',['Yes'=>'Yes','No'=>'No'],$user->is_email_verified,array('class'=>'form-control','required'=>true)) }}
                </div-->
              </div>

              <div class="form-row">
                <div class="form-group col-md-6">
                  <label class="col-form-label">Date of Birth</label>
                  {{Form::text('dob',$user->getMeta('dob'),array('class'=>'form-control','disabled'=>true)) }}
                </div>
                <div class="form-group col-md-6">
                  <label class="col-form-label">Gender</label>
                  {{Form::select('gender',['Male'=>'Male','Female'=>'Female'],$user->getMeta('gender'),array('class'=>'form-control','required'=>true) ) }}
                </div>
              </div>

              <div class="form-row">
                <div class="form-group col-md-12">
                  <label class="col-form-label">Address</label>
                  {{Form::textarea('address',$user->getMeta('address'),array('class'=>'form-control')) }}
                </div>
              </div>
              <button type="submit" class="btn btn-primary">SUBMIT</button>
            {{ Form::close() }}
        </div>
    </div>
  </div>
</div>
@endsection