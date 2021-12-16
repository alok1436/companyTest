@extends('admin.layouts.default') 
@section('pageTitle', 'Admin | Add Class') 
@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card-box">
                <h4 class="m-t-0 header-title">Add New Class</h4>
                {{ Form::open(array('url' => 'admin/class/store', 'files' => true, 'class' => '','id'=>'form')) }}
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label class="col-form-label">Name</label>
                            {{Form::text('name',null,array('class'=>'form-control')) }}
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">SUBMIT</button>
                {{ Form::close() }}
            </div>
        </div>
    </div>
</div>
@endsection