@extends('admin.layouts.default') 
@section('pageTitle', 'Admin | Add Student') 
@section('content')
<div class="container-fluid">
  <div class="row">
    <div class="col-md-12">
        <div class="card-box">
            <h4 class="m-t-0 header-title">Add New Student</h4>
            {{ Form::open(array('url' => 'admin/student/store', 'files' => true, 'class' => '','id'=>'form')) }}
              <div class="form-row">
                <div class="form-group col-md-6">
                  <label class="col-form-label">First Name</label>
                  {{Form::text('first_name',null,array('class'=>'form-control' )) }}
                </div>
                <div class="form-group col-md-6">
                  <label class="col-form-label">Last Name</label>
                  {{Form::text('last_name',null,array('class'=>'form-control' )) }}
                </div>
              </div>
              <div class="form-row">
                <div class="form-group col-md-6">
                  <label class="col-form-label">Email</label>
                  {{Form::email('email',null,array('class'=>'form-control' )) }}
                </div>
                <div class="form-group col-md-6">
                  <label class="col-form-label">Password</label>
                  {{Form::password('password',array('class'=>'form-control' )) }}
                </div>
              </div>
              <div class="form-row">
                <div class="form-group col-md-6">
                  <label class="col-form-label">Phone</label>
                  {{Form::text('phone',null,array('class'=>'form-control')) }}
                </div> 
                <div class="form-group col-md-6">
                  <label class="col-form-label">Class</label>
                  {{Form::select('class_id',$classes,null,array('class'=>'form-control')) }}
                </div>
              </div>
              <div class="form-row">
                <div class="form-group col-md-6">
                  <label class="col-form-label">Profile</label>
                  {{Form::file('user_profile',array('class'=>'form-control' )) }}
                </div>
              </div>
              <button type="submit" class="btn btn-primary">SUBMIT</button>
            {{ Form::close() }}
        </div>
    </div>
  </div>
</div>
@endsection