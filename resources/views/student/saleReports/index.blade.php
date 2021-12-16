@extends('vendor.layouts.default')
@section('pageTitle', 'Vendor | Sale Reports') 
@section('content')

<div class="container-fluid">
  <div class="row">
    <div class="col-12">
      <div class="card-box">
        <div class="dropdown float-right">
          <a href="{{ url('vendor/staff/store') }}" class="btn btn-primary btn-rounded"><i class="fa fa-plus"></i> Add New Sale Report</a>
        </div>
        <h4 class="mt-0 mb-3 header-title">Sale Reports</h4>

        <table id="datatable" class="table">
          <thead>
            <tr>
              <th>#</th>
              <th>Name</th>
              <th>Slug</th>
              <th>Status</th>
              <th class="text-center">Action</th>
            </tr>
          </thead>
          <tbody>
            <?php $i=1; ?>
            @foreach($saleReports as $saleReport)
            <tr>
              <td>{{ $i++ }}</td>
              <td>{{ $saleReport->name }}</td>
              <td>{{ $saleReport->slug }}</td>
              <td>
                @if($saleReport->status=='Enable')
                  <a href="#" class="badge badge-success">Enable</a>
                @else
                  <a href="#" class="badge badge-danger">Disable</a>
                @endif
              </td>
              <td>
                <div class="dropdown text-center">
                  <a href="#" class="dropdown-toggle arrow-none card-drop" data-toggle="dropdown" aria-expanded="false">
                      <i class="mdi mdi-dots-vertical"></i>
                  </a>
                  <div class="dropdown-menu dropdown-menu-right">
                    <a class="dropdown-item" href="{{ url('vendor/saleReport/edit/'.$saleReport->id) }}">
                      <i class="fas fa-user-edit"></i> Edit
                    </a>
                    <a class="dropdown-item" href="{{ url('vendor/saleReport/delete/'.$saleReport->id) }}" onclick="return confirm('Are you sure?')">
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