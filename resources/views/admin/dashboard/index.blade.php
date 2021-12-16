@extends('admin.layouts.default')
@section('pageTitle', 'Admin | Dashboard')
@section('content')

<!-- Start Content-->
<div class="container-fluid">
  <div class="row">
    <div class="col-xl-2 col-md-6">
      <div class="card-box">
        <h4 class="header-title mt-0 mb-4">Classes</h4>       
            <h2 class="font-weight-normal pt-1 mb-1"><strong><a href="{{ url('admin/customer') }}">0</a></strong></h2>
      </div>
    </div>
    <div class="col-xl-2 col-md-6">
      <div class="card-box">
        <h4 class="header-title mt-0 mb-4">Students</h4>       
            <h2 class="font-weight-normal pt-1 mb-1"><strong><a href="{{ url('admin/vendors') }}">0</a></strong></h2>
      </div>
    </div>
</div>
@endsection