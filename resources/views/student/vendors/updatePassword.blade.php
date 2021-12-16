@extends('vendor.layouts.default') 
@section('pageTitle', 'Vendor | Update Password') 
@section('content')

<div class="container-fluid">
  <div class="row">
    <div class="col-md-12">
        <div class="card-box">
            <h4 class="m-t-0 header-title">Change Password</h4>
            {{ Form::open(array('url' => 'vendor/change/password', 'class' => '','id'=>'form')) }}
              <div class="form-row">
                <div class="form-group col-md-6">
                  <label class="col-form-label">New Password</label>
                  {{Form::password('new_password',array('class'=>'form-control','required'=>true)) }}
                </div>
                <div class="form-group col-md-6">
                  <label class="col-form-label">Confirm Password</label>
                  {{Form::password('confirm_password',array('class'=>'form-control') ) }} 
                </div>
              </div>

              
              <button type="submit" class="btn btn-primary">SUBMIT</button>
            {{ Form::close() }}
        </div>
    </div>
  </div>
</div>
@endsection