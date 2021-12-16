@extends(Auth::user()->roles()->first()->name.'.layouts.default') 
@section('pageTitle', 'Admin | Variant Options') 
@section('content')

<div class="container-fluid">
  <div class="row">
    <div class="col-12">
      <div class="card-box">
        <div class="dropdown float-right"><!---href="{{ url('vendor/variantOption/store/'.$id ) }}"-->
          <a href="{{ url(Auth::user()->roles()->first()->name.'/variants') }}" class="btn btn-warning btn-rounded">Back</a>
          <a data-toggle="modal" data-target="#variantOptionModal" href="javascript:void(0)" data-data='<?php echo json_encode($id); ?>' class="btn btn-primary btn-rounded addVariantOption"><i class="fa fa-plus"></i> Add New Variant Option</a>
        </div>
        <h4 class="mt-0 mb-3 header-title">Variant Options </h4>

        <table id="datatable" class="table table-bordered dt-responsive nowrap">
          <thead>
            <tr>
              <th>#</th>
              <!-- <th>Variant Name</th> -->
              <th>Variant option Name</th>
              <th class="text-center">Action</th>
            </tr>
          </thead>
          <tbody>
            <?php $i=1; ?>
            @foreach($variantOptions as $variantOption)
            <tr>
              <td>{{ $i++ }}</td>
              <!-- <td>{{ $variantOption->variant_name }}</td> -->
              <td>{{ $variantOption->name }}</td>
              <td>
                <div class="dropdown text-center">
                  <a href="#" class="dropdown-toggle arrow-none card-drop" data-toggle="dropdown" aria-expanded="false">
                      <i class="mdi mdi-dots-vertical"></i>
                  </a>
                  <div class="dropdown-menu dropdown-menu-right">
                    <a class="dropdown-item editVariantOption" data-data='<?php echo json_encode($variantOption); ?>' href="javascript:void(0)">
                      <i class="fas fa-user-edit"></i> Edit
                    </a>
                    <a class="dropdown-item" href="{{ url(Auth::user()->roles()->first()->name.'/variantOption/delete/'.$variantOption->id) }}" onclick="return confirm('Are you sure?')">
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
<div id="variantOptionModal" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title exp_heading">Add New Variant Option</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
        <!--begin::Form-->
        {{ Form::open(array('url' => Auth::user()->roles()->first()->name.'/variantOption/store/'.$id, 'files' => true, 'class' => '','id'=>'form', 'enctype'=>'multipart/form-data')) }}
          <input type="hidden" name="id" id="hid_id" value="0">
          <div class="row">
            <div class="col-12 col-md-12">
              <div class="form-group">
                <label>Name</label>
                {{Form::text('name',null,array('class'=>'form-control','id'=>'name','required'=>true) ) }}
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