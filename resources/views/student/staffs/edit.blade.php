@extends('vendor.layouts.default') 
@section('pageTitle', 'Vendor | Edit Staff') 
@section('content')

<div class="container-fluid">
  <div class="row">
    <div class="col-md-12">
        <div class="card-box">
            <h4 class="m-t-0 header-title">Edit Staff</h4>
            {{ Form::open(array('url' => 'vendor/staff/edit/'.$user->id, 'files' => true, 'class' => '','id'=>'form', 'enctype'=>'multipart/form-data')) }}
              <div class="form-row">
                <div class="form-group col-md-6">
                  <label class="col-form-label">First Name</label>
                  {{ Form::text('first_name',$user->first_name,array('class'=>'form-control','required'=>true)) }}
                </div>
                <div class="form-group col-md-6">
                  <label class="col-form-label">Last Name</label>
                  {{Form::text('last_name',$user->last_name,array('class'=>'form-control','required'=>true)) }}
                </div>
              </div>

              <div class="form-row">
                <div class="form-group col-md-6">
                  <label class="col-form-label">Email</label>
                  {{ Form::email('email',$user->email,array('class'=>'form-control','required'=>true) ) }}
                </div>
                <div class="form-group col-md-6">
                  <label class="col-form-label">Password</label>
                  {{ Form::password('password',array('class'=>'form-control')) }}
                  <em style="color: red;">Leave blank if you don't want to change</em>
                </div>
              </div>

              <div class="form-row">
                <div class="form-group col-md-6">
                  <label class="col-form-label">Phone</label>
                  {{ Form::text('phone',$user->phone,array('class'=>'form-control','required'=>true)) }}
                </div>
                <div class="form-group col-md-6">
                  <label class="col-form-label">Date of Birth</label>
                  {{ Form::date('dob',$user->dob,array('class'=>'form-control','required'=>true,'max'=>date('Y-m-d',strtotime('31-07-2004')))) }}
                </div>
              </div>

              <div class="form-row">
                <div class="form-group col-md-6">
                  <label class="col-form-label">Image</label>
                  {{ Form::file('profile',array('class'=>'form-control')) }}
                  @if($user->user_profile != '')
                  <img width="28" height="28" src="{{ asset($user->user_profile) }}" class="rounded-circle">
                  @endif
                </div>
                <div class="form-group col-md-6">
                  <label class="col-form-label">Status</label>
                  {{ Form::select('status',['Active'=>'Active','Inactive'=>'Inactive'],$user->status,array('class'=>'form-control')) }}
                </div>
              </div>

              <div class="form-row">
                <div class="form-group col-md-6">
                  <label class="col-form-label">Department</label>
                  {{ Form::text('department',$user->getMeta('department'),array('class'=>'form-control','required'=>true)) }}
                </div>
                 <div class="form-group col-md-6">
                  <label class="col-form-label">Position</label>
                  {{ Form::text('position',$user->getMeta('position'),array('class'=>'form-control','required'=>true)) }}
                </div>
              </div>

              <div class="form-row">
                <div class="form-group col-md-12">
                  <label class="col-form-label">Address</label>
                  {{Form::textarea('address',$user->getMeta('address'),array('class'=>'form-control','rows'=>5)) }}
                </div>
              </div>
              
              <button type="submit" class="btn btn-primary">SUBMIT</button>
            {{ Form::close() }}
        </div>
    </div>
  </div>
</div>
@endsection