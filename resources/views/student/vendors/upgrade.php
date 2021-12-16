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