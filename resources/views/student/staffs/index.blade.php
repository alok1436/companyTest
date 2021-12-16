@extends('vendor.layouts.default')
@section('pageTitle', 'Vendor | Staff') 
@section('content')

<div class="container-fluid">
  <div class="row">
    <div class="col-12">
      <div class="card-box">
        <div class="dropdown float-right">
          <a href="{{ url('vendor/staff/store') }}" class="btn btn-primary btn-rounded"><i class="fa fa-plus"></i> Add New Staff</a>
        </div>
        <h4 class="mt-0 mb-3 header-title">Staff</h4>

        <table id="datatable" class="table">
          <thead>
            <tr>
              <th>#</th>
              <th>Profile</th>
              <th>Name</th>
              <th>Email</th>
              <th>Phone</th>
              <th>Address</th>
              <th>DOB</th>
              <th>Status</th>
              <th class="text-center">Action</th>
            </tr>
          </thead>
          <tbody>
            <?php $i=1; ?>
            @foreach($staffs as $staff)
            <tr>
              <td>{{ $i++ }}</td>
              <td>
                @if($staff->user_profile != '')
                  <img width="28" height="28" src="{{ asset($staff->user_profile) }}" class="rounded-circle">
                @endif
                </td>
              <td><a href="{{ url('vendor/staff/edit/'.$staff->id) }}">{{ $staff->first_name }} {{ $staff->last_name }}</a></td>
              <td>{{ $staff->email }}</td>
              <td>{{ $staff->phone }}</td>
              <td>{{ $staff->address }}</td>
              <td>{{ $staff->dob }}</td>
              <td>
                <a href="#" class="badge badge-{{ $staff->status == 'Active' ? 'success' : 'danger' }}">{{ $staff->status }}</a>
              </td>
              <td>
                <div class="dropdown text-center">
                  <a href="#" class="dropdown-toggle arrow-none card-drop" data-toggle="dropdown" aria-expanded="false">
                      <i class="mdi mdi-dots-vertical"></i>
                  </a>
                  <div class="dropdown-menu dropdown-menu-right">
                    <a class="dropdown-item" href="{{ url('vendor/staff/edit/'.$staff->id) }}">
                      <i class="fas fa-user-edit"></i> Edit
                    </a>
                    <a class="dropdown-item" href="{{ url('vendor/staff/delete/'.$staff->id) }}" onclick="return confirm('Are you sure?')">
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