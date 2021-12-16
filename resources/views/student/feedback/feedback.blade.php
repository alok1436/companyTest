@extends('vendor.layouts.default') 
@section('pageTitle', 'Vendor | feedback') 
@section('content')

<div class="container-fluid">
  <div class="row">
    <div class="col-md-12">
        <div class="card-box">
            <h4 class="m-t-0 header-title">Feedback</h4>
            {{ Form::open(array('url' => 'vendor/feedback-store', 'files' => true, 'class' => '','id'=>'form')) }}
              <div class="form-row">
                <div class="form-group col-md-12">
                  <label class="col-form-label">Title</label>
                  {{Form::text('title',null,array('class'=>'form-control','required'=>true)) }}
                </div>
                <div class="form-group col-md-12">
                  <label class="col-form-label">Message</label>
                  {{Form::textarea('msg',null,array('class'=>'form-control','required'=>true,'id'=>'summary-ckeditor')) }}
                </div>
              </div>
              <button type="submit" class="btn btn-primary">SUBMIT</button>
            {{ Form::close() }}
        </div>
    </div>
  </div>
</div>
@endsection