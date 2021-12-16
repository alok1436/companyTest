@extends('admin.layouts.default')
@section('pageTitle', 'Admin | Classes') 
@section('content')

<div class="container-fluid">
  <div class="row">
    <div class="col-12">
      <div class="card-box">
        <div class="dropdown float-right">
          <a href="{{ url('admin/class/store') }}" class="btn btn-primary btn-rounded addBrand"><i class="fa fa-plus"></i> Add New Class</a>
        </div>
        <h4 class="mt-0 mb-3 header-title">Classes</h4>

        <table id="datatable" class="table">
          <thead>
            <tr>
              <th>#</th>
              <th>Class Name</th>
              <th class="text-center">Action</th>
            </tr>
          </thead>
          <tbody>
            <?php $i=1; ?>
            @foreach($classes as $class)
            <tr>
              <td>{{ $i++ }}</td>
              <td><a href="{{ url('admin/class/edit/'.$class->id) }}">{{ $class->name }}</a></td>
              <td>
                <div class="dropdown text-center">
                  <a href="#" class="dropdown-toggle arrow-none card-drop" data-toggle="dropdown" aria-expanded="false">
                      <i class="mdi mdi-dots-vertical"></i>
                  </a>
                  <div class="dropdown-menu dropdown-menu-right">
                    <a class="dropdown-item" href="{{ url('admin/class/edit/'.$class->id) }}">
                      <i class="fa fa-edit"></i> Edit
                    </a>
                    <a class="dropdown-item" href="{{ url('admin/class/delete/'.$class->id) }}" onclick="return confirm('Are you sure?')">
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