@extends('vendor.layouts.default')
@section('pageTitle', 'Vendor | Affilates') 
@section('content')

<div class="container-fluid">
  <div class="row">
    <div class="col-12">
      <div class="card-box">
        <div class="dropdown float-right">
          <a href="{{ url('vendor/affilate/store') }}" class="btn btn-primary btn-rounded"><i class="fa fa-plus"></i> Add New Affilate</a>
        </div>
        <h4 class="mt-0 mb-3 header-title">Affilate</h4>

        <table id="datatable" class="table">
          <thead>
            <tr>
              <th>#</th>
              <th>Code</th>
              <th>Commission</th>
              <th>Discount</th>
              <th class="text-center">Action</th>
            </tr>
          </thead>
          <tbody>
            <?php $i=1; ?>
            @foreach($affilates as $affilate)
            <tr>
              <td>{{ $i++ }}</td>
              <td>{{ $affilate->code }}</td>
              <td>{{ $affilate->commission }}</td>
              <td>{{ $affilate->discount }}</td>
              <td>
                <div class="dropdown text-center">
                  <a href="#" class="dropdown-toggle arrow-none card-drop" data-toggle="dropdown" aria-expanded="false">
                      <i class="mdi mdi-dots-vertical"></i>
                  </a>
                  <div class="dropdown-menu dropdown-menu-right">
                    <a class="dropdown-item" href="{{ url('admin/affilate/edit/'.$affilate->id) }}">
                      <i class="fas fa-user-edit"></i> Edit
                    </a>
                    <a class="dropdown-item" href="{{ url('admin/affilate/delete/'.$affilate->id) }}" onclick="return confirm('Are you sure?')">
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