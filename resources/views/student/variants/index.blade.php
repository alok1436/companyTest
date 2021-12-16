@extends(Auth::user()->roles()->first()->name.'.layouts.default') 
@section('pageTitle', 'Vendor | Variants') 
@section('content')

<div class="container-fluid">
  <div class="row">
    <div class="col-12">
      <div class="card-box">
        <div class="dropdown float-right">
          @if(Auth::user()->roles()->first()->name == 'admin')
          <a data-toggle="modal" data-target="#brandModal" href="javascript:void(0)" class="btn btn-primary btn-rounded addVariant"><i class="fa fa-plus"></i> Add New Variant</a>
          @endif
        </div>
        <h4 class="mt-0 mb-3 header-title">Variants</h4>

        <table id="datatable" class="table nowrap">
          <thead>
            <tr>
              <th>Name</th>
              <th>Type</th>
              <th class="text-center">Manage Variant Options</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            @foreach($variants as $variant)
            <tr>
              <td>{{ $variant->name }}</td>
              <td>{{ $variant->type }}</td>
              <td class="text-center">
                <a href="{{ url(Auth::user()->roles()->first()->name.'/variantOptions/'.$variant->id) }}" class="btn btn-info btn-rounded btn-sm"> Manage Variant Options</a>
              </td>
              <td>
                <div class="dropdown float-right">
                  <a href="#" class="dropdown-toggle arrow-none card-drop" data-toggle="dropdown" aria-expanded="false">
                      <i class="mdi mdi-dots-vertical"></i>
                  </a>
                  <div class="dropdown-menu dropdown-menu-right">
                    <a class="dropdown-item editVariant" data-data='<?php echo json_encode($variant); ?>' href="javascript:void(0)">
                      <i class="fas fa-user-edit"></i> Edit
                    </a>
                    <a class="dropdown-item" href="{{ url(Auth::user()->roles()->first()->name.'/variant/delete/'.$variant->id) }}" onclick="return confirm('Are you sure?')">
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


<!-- Modal -->
<div id="variantModal" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title exp_heading">Add New Variant</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
        <!--begin::Form-->
        {{ Form::open(array('url' => Auth::user()->roles()->first()->name.'/variant/store', 'files' => true, 'class' => '','id'=>'form', 'enctype'=>'multipart/form-data')) }}
          <input type="hidden" name="id" id="hid_id" value="0">
          <div class="row">
            <div class="col-12 col-md-12">
              <div class="form-group">
                <label>Name</label>
                {{Form::text('name',null,array('class'=>'form-control','id'=>'name','required'=>true)) }}
              </div>
            </div>
          </div>
          <div class="m-t-20">
            <button type="submit" class="btn btn-primary btn-rounded float-right">SUBMIT</button>
          </div>
        {{ Form::close() }}
        <!--end::Form-->
      </div>
    </div>
  </div>
</div>
@endsection