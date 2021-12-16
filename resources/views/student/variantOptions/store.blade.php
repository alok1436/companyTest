@extends(Auth::user()->roles()->first()->name.'.layouts.default')  
@section('pageTitle', 'Vendor | Add Variant Option') 
@section('content')

<div class="container-fluid">
  <div class="row">
    <div class="col-md-12">
        <div class="card-box">
            <h4 class="m-t-0 header-title">Add New Variant Option</h4>
            {{ Form::open(array('url' => Auth::user()->roles()->first()->name.'/variantOption/store/'.$variants->id, 'files' => true, 'class' => '','id'=>'form')) }}
              <div class="form-row">
                <!-- <div class="form-group col-md-6">
                  <label class="col-form-label">Select Variant</label>
                  {{Form::select('variant_id',$variants,null,array('class'=>'form-control','required'=>true)) }}
                </div> -->
                <div class="form-group col-md-6">
                  <label class="col-form-label">Variant Option Name</label>
                  {{Form::text('name',null,array('class'=>'form-control','required'=>true) ) }}
                </div>
              </div>
              <button type="submit" class="btn btn-primary">SUBMIT</button>
            {{ Form::close() }}
        </div>
    </div>
  </div>
</div>
@endsection