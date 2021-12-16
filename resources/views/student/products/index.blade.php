@extends('vendor.layouts.default')
@section('pageTitle', 'Vendor | Products') 
@section('content')

<div class="container-fluid">
  <div class="row">
    <div class="col-12">
      <div class="card-box">
        <div class="dropdown float-right">
          <a href="{{ url('vendor/product/store') }}" class="btn btn-primary btn-rounded addBrand"><i class="fa fa-plus"></i> Add New Product</a>
        </div>
        <h4 class="mt-0 mb-3 header-title">Products</h4>

        <table id="datatable" class="table">
          <thead>
            <tr>
              <th>#</th>
              <th>Image</th>
              <th>Name</th>
              <th>Price</th>
              <th>Sale Price</th>
              <th class="text-center">Action</th>
            </tr>
          </thead>
          <tbody>
            <?php $i=1; ?>
            @foreach($products as $product)
             
            <tr>
              <td>{{ $i++ }}</td>
              <td>
                <img width="35" height="35" src="{{asset($product->getMeta('featured_image')) }}" class="rounded-circle">
              </td>
              <td>
                <a href="{{ url('vendor/product/edit/'.$product->id) }}">{{ $product->name }}</a>
              </td>
              <td>{{ $product->skus->regular_price }}</td>
              <td>{{ $product->skus->sale_price }}</td>
              <td>
                <div class="dropdown text-center">
                  <a href="#" class="dropdown-toggle arrow-none card-drop" data-toggle="dropdown" aria-expanded="false">
                      <i class="mdi mdi-dots-vertical"></i>
                  </a>
                  <div class="dropdown-menu dropdown-menu-right">
                    <a class="dropdown-item" href="{{ url('vendor/product/edit/'.$product->id) }}">
                      <i class="fas fa-user-edit"></i> Edit
                    </a>
                    <a class="dropdown-item" href="{{ url('vendor/product/delete/'.$product->id) }}" onclick="return confirm('Are you sure?')">
                      <i class="fas fa-trash"></i> Delete
                    </a>
                  </div>
                </div>
              </td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </div> 
  <!-- end row -->
</div>

@endsection