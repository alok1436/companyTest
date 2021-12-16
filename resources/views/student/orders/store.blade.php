@extends('vendor.layouts.default') 
@section('pageTitle', 'Vendor | Add Order') 
@section('content')

<div class="container-fluid">
  <div class="row">
    <div class="col-md-12">
        <div class="card-box">
            <h4 class="m-t-0 header-title">Add New Order</h4>
            {{ Form::open(array('url' => 'vendor/order/store', 'files' => true, 'class' => '','id'=>'form')) }}
              <div class="form-row">
                <div class="form-group col-md-6">
                  <label class="col-form-label">Name</label>
                  {{Form::text('name',null,array('class'=>'form-control','required'=>true)) }}
                </div>
                <div class="form-group col-md-6">
                  <label class="col-form-label">Price</label>
                  {{Form::text('price',null,array('class'=>'form-control','required'=>true)) }}
                </div>
              </div>

              <div class="form-row">
                <div class="form-group col-md-6">
                  <label class="col-form-label">Shipping Charge</label>
                  {{Form::text('shipping_charge',null,array('class'=>'form-control','required'=>true) ) }}
                </div>
                <div class="form-group col-md-6">
                  <label class="col-form-label">Quantity</label>
                  {{ Form::text('quantity',null,array('class'=>'form-control','required'=>true)) }}
                </div>
              </div>

              <button type="submit" class="btn btn-primary">SUBMIT</button>
            {{ Form::close() }}
        </div>
    </div>
  </div>
</div>
@endsection