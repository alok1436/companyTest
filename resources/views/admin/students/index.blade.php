@extends('admin.layouts.default')
@section('pageTitle', 'Admin | Banners') 
@section('content')

<div class="container-fluid">
  <div class="row">
    <div class="col-12">
      <div class="card-box">
        <div class="dropdown float-right">
          <a href="{{ url('admin/student/store') }}" class="btn btn-primary btn-rounded addBrand"><i class="fa fa-plus"></i> Add New Student</a>
        </div>
        <h4 class="mt-0 mb-3 header-title">Students</h4>

        <table id="datatable" class="table">
          <thead>
            <tr>
              <th>#</th>
              <th>Profile</th>
              <th>Name</th>
              <th>Email</th>
              <th>Phone</th>
              <th>Class</th>
              <th class="text-center">Action</th>
            </tr>
          </thead>
          <tbody>
            <?php $i=1; ?>
            @foreach($students as $student)
            <tr>
              <td>{{ $i++ }}</td>
              <td>
                @if(isset($student->image) && $student->image !=null)
                  <img width="28" height="28" src="{{ asset($student->image) }}" class="rounded-circle" />
                @endif
              </td>
              <td><a href="{{ url('admin/student/edit/'.$student->id) }}">{{ $student->full_name }}</a></td>
              <td>{{ $student->email }}</td>
              <td>{{ $student->phone }}</td>
              <td></td>
              <td>
                <div class="dropdown text-center">
                  <a href="#" class="dropdown-toggle arrow-none card-drop" data-toggle="dropdown" aria-expanded="false">
                      <i class="mdi mdi-dots-vertical"></i>
                  </a>
                  <div class="dropdown-menu dropdown-menu-right">
                    <a class="dropdown-item" href="{{ url('admin/student/edit/'.$student->id) }}">
                      <i class="fa fa-edit"></i> Edit
                    </a>
                    <a class="dropdown-item" href="{{ url('admin/student/delete/'.$student->id) }}" onclick="return confirm('Are you sure?')">
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