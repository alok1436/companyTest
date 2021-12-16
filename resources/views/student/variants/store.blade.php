@extends(Auth::user()->roles()->first()->name.'.layouts.default') 
@section('pageTitle', 'Vendor | Add Variant') 
@section('content')

<div class="container-fluid">
  <div class="row">
    <div class="col-md-12">
        <div class="card-box">
            <h4 class="m-t-0 header-title">Add New Variant</h4>
            {{ Form::open(array('url' => 'vendor/variant/store', 'files' => true, 'class' => '','id'=>'form', 'enctype'=>'multipart/form-data')) }}
              <div class="form-row">
                <div class="form-group col-md-6">
                  <label class="col-form-label">Name</label>
                  {{Form::text('name',null,array('class'=>'form-control','required'=>true)) }}
                </div>
              </div>
              <button type="submit" class="btn btn-primary">SUBMIT</button>
            {{ Form::close() }}
        </div>
    </div>
  </div>
</div>
@endsection