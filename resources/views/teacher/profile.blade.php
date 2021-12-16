@extends('delivery.layouts.default') 
@section('pageTitle', 'Delivery | Edit Profile') 
@section('content')

<div class="container-fluid">
  <div class="row">
    <div class="col-md-12">
        <div class="card-box">
            <h4 class="m-t-0 header-title">Delivery Profile</h4>
            {{ Form::open(array('url' => 'delivery/profile', 'files' => true, 'class' => '','id'=>'form', 'enctype'=>'multipart/form-data')) }}
              

              <div class="form-row">
                <div class="form-group col-md-6">
                  <label class="col-form-label">First Name</label>
                  {{Form::text('first_name',$user->first_name,array('class'=>'form-control','required'=>true) ) }}
                </div>
                <div class="form-group col-md-6">
                  <label class="col-form-label">Last Name</label>
                  {{Form::text('last_name',$user->last_name,array('class'=>'form-control','required'=>true) ) }}
                </div>
              </div>

              <div class="form-row">
                <div class="form-group col-md-6">
                  <label class="col-form-label">Email</label>
                  {{Form::email('email',$user->email,array('class'=>'form-control','required'=>true)) }}
                </div>
                <div class="form-group col-md-6">
                  <label class="col-form-label">Password</label>
                  {{Form::password('password',array('class'=>'form-control')) }}
                  <em>Leave blank if you don't want to change</em>
                </div>
                
                
                <!--div class="form-group col-md-3">
                  <label class="col-form-label">Email Verfied</label>
                  {{Form::select('is_email_verified',['Yes'=>'Yes','No'=>'No'],$user->is_email_verified,array('class'=>'form-control','required'=>true)) }}
                </div-->
              </div>

              <div class="form-row">
                <div class="form-group col-md-6">
                  <label class="col-form-label">Phone</label>
                  {{Form::text('phone',$user->phone,array('class'=>'form-control','required'=>true) ) }}
                </div>
                <div class="form-group col-md-6">
                  <label class="col-form-label">Profile</label>
                  {{Form::file('user_profile',array('class'=>'form-control') ) }}
                  @if($user->user_profile)
                  <img width="35" height="35" src="{{ asset($user->user_profile) }}" class="rounded-circle">
                  @endif
                </div>
              </div>

              <div class="form-row">
                <div class="form-group col-md-6">
                  <label class="col-form-label">Status</label>
                  {{Form::select('status',['Active'=>'Active','Inactive'=>'Inactive'],$user->status,array('class'=>'form-control','required'=>true)) }}
                </div>
                <div class="form-group col-md-6">
                  <label class="col-form-label">Joining Date</label>
                  {{Form::date('joining_date',$user->getMeta('joining_date'),array('class'=>'form-control','required'=>true,'max'=>date('Y-m-d')) ) }}
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
                <div class="form-group col-md-12">
                  <label class="col-form-label">Current Address</label>
                  {{Form::textarea('current_address',$user->getMeta('current_address'),array('class'=>'form-control','rows'=>5)) }}
                </div>
              </div>

              <div class="form-row">
                <div class="form-group col-md-12">
                  <label class="col-form-label">Permanent Address</label>
                  {{Form::textarea('permanent_address',$user->getMeta('permanent_address'),array('class'=>'form-control','rows'=>5)) }}
                </div>
              </div>

              <button type="submit" class="btn btn-primary">SUBMIT</button>
            {{ Form::close() }}
        </div>
    </div>
  </div>
</div>
@endsection