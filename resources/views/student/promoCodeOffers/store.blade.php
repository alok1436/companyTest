@extends('vendor.layouts.default') 
@section('pageTitle', 'Vendor | Add Staff') 
@section('content')

<div class="container-fluid">
  <div class="row">
    <div class="col-md-12">
        <div class="card-box">
            <h4 class="m-t-0 header-title">Add New Staff</h4>
            {{ Form::open(array('url' => 'vendor/staff/store', 'files' => true, 'class' => '','id'=>'form', 'enctype'=>'multipart/form-data')) }}
              <div class="form-row">
                <div class="form-group col-md-6">
                  <label class="col-form-label">First Name</label>
                  {{ Form::text('first_name',null,array('class'=>'form-control','required'=>true)) }}
                </div>
                <div class="form-group col-md-6">
                  <label class="col-form-label">Last Name</label>
                  {{Form::text('last_name',null,array('class'=>'form-control','required'=>true)) }}
                </div>
              </div>

              <div class="form-row">
                <div class="form-group col-md-6">
                  <label class="col-form-label">Email</label>
                  {{ Form::email('email',null,array('class'=>'form-control','required'=>true) ) }}
                </div>
                <div class="form-group col-md-6">
                  <label class="col-form-label">Phone</label>
                  {{ Form::text('phone',null,array('class'=>'form-control','required'=>true)) }}
                </div>
              </div>

              <div class="form-row">
                <div class="form-group col-md-6">
                  <label class="col-form-label">Date of Birth</label>
                  {{ Form::date('dob',null,array('class'=>'form-control','required'=>true)) }}
                </div>
                 <div class="form-group col-md-6">
                  <label class="col-form-label">Image</label>
                  {{ Form::file('profile',array('class'=>'form-control')) }}
                </div>
              </div>

              <div class="form-row">
                <div class="form-group col-md-6">
                  <label class="col-form-label">Department</label>
                  {{ Form::text('department',null,array('class'=>'form-control','required'=>true)) }}
                </div>
                 <div class="form-group col-md-6">
                  <label class="col-form-label">Position</label>
                  {{ Form::text('position',null,array('class'=>'form-control','required'=>true)) }}
                </div>
              </div>

              <div class="form-row">
                <div class="form-group col-md-6">
                  <label class="col-form-label">Status</label>
                  {{ Form::select('status',['Active'=>'Active','Inctive'=>'Inctive'],null,array('class'=>'form-control')) }}
                </div>
              </div>

              <div class="form-row">
                <div class="form-group col-md-12">
                  <label class="col-form-label">Address</label>
                  {{Form::textarea('address',null,array('class'=>'form-control','rows'=>5)) }}
                </div>
              </div>
              
              <button type="submit" class="btn btn-primary">SUBMIT</button>
            {{ Form::close() }}
        </div>
    </div>
  </div>
</div>
@endsection