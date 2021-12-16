@extends('vendor.layouts.default') 
@section('pageTitle', 'Vendor | Edit Order') 
@section('content')

<div class="container-fluid">
  <div class="row">
    <div class="col-md-12">
        <div class="card-box">
            <h4 class="m-t-0 header-title">Add New Edit</h4>
            {{ Form::open(array('url' => 'vendor/order/edit/'.$order->id, 'files' => true, 'class' => '','id'=>'form')) }}
              <div class="form-row">
                <div class="form-group col-md-6">
                  <label class="col-form-label">Name</label>
                  {{Form::text('name',$order->name,array('class'=>'form-control','required'=>true)) }}
                </div>
                <div class="form-group col-md-6">
                  <label class="col-form-label">Price</label>
                  {{Form::text('price',$order->price,array('class'=>'form-control','required'=>true)) }}
                </div>
              </div>

              <div class="form-row">
                <div class="form-group col-md-6">
                  <label class="col-form-label">Shipping Charge</label>
                  {{Form::text('shipping_charge',$order->shipping_charge,array('class'=>'form-control','required'=>true) ) }}
                </div>
                <div class="form-group col-md-6">
                  <label class="col-form-label">Quantity</label>
                  {{ Form::text('quantity',$order->quantity,array('class'=>'form-control','required'=>true)) }}
                </div>
              </div>

              <button type="submit" class="btn btn-primary">SUBMIT</button>
            {{ Form::close() }}
        </div>
    </div>
  </div>
</div>
@endsection