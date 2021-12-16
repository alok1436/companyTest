@extends('delivery.layouts.default')
@section('pageTitle', 'Delivey | Orders') 
@section('content')

<div class="container-fluid">
  <div class="row">
    <div class="col-12">
      <div class="card-box">
        <div class="col-12">
        <form class="form-inline" action="" method="get">
          <label class="sr-only" for="order_code">Order or sub order code</label>
          <input type="text" class="form-control mb-2 mr-sm-2" name="sub_order_code" placeholder="Order or sub order code" value="{{request()->sub_order_code }}">

          <label class="sr-only" for="order_code">From Date</label>
          <input type="date" class="form-control mb-2 mr-sm-2" name="from_date" placeholder="From Date" value="{{request()->from_date }}" format="yyyy-mm-dd">

          <label class="sr-only" for="order_code">To Date</label>
          <input type="date" class="form-control mb-2 mr-sm-2" name="to_date" placeholder="To Date" value="{{request()->to_date }}" format="yyyy-mm-dd">

          <label class="sr-only" for="order_status">Status</label>
          {!! csrf_field() !!}
          <select type="text" class="form-control mb-2 mr-sm-2" name="status" id="order_status" placeholder="Enter order code">
            <?php $status_value = ['Select status','Dispatch','Completed','Cancelled'];
                foreach ($status_value as $value) {
                    echo '<option value="'.$value.'"'. (ucfirst(request()->status) == $value ||  request()->order_status == $value ? "selected" : "").'>'.$value.'</option>';
                }
              ?>
          </select>
          <?php //dd(request()->all()); ?>
          <button type="submit" name="action" value="submit_filter" name="true" class="btn btn-primary mb-2">Filter</button>
          @if(\Request::get('action') == 'submit_filter')
          &nbsp;<a href="" onclick="location.reload()" class="btn btn-warning mb-2">Clear filter</a>
          @endif
        </form>

      </div>
      </div>
    </div>
  </div>
  <div class="row">
    <div class="col-12">
      <div class="card-box">
        <div class="dropdown float-right">
          <!---a href="{{ url('vendor/order/store') }}" class="btn btn-primary btn-rounded"><i class="fa fa-plus"></i> Add New Order</a-->
        </div>
        <h4 class="mt-0 mb-3 header-title">Orders</h4>

        <table id="datatable" class="table">
          <thead>
            <tr>
              <th>#</th>
              <th>Name</th>
              <th>Sub Order Code</th>
              <th>Price</th>
              <th>Total Price</th>
              <th>Quantity</th>
              <th>Status</th>
              <th class="text-center">Action</th>
            </tr>
          </thead>
          <tbody>
            <?php $i=1; ?>
            @foreach($orders as $order)
            <tr>
              <td>{{ $i++ }}</td>
              <td>{{ $order->item_name }}</td>
              <td>{{ $order->sub_order_code }}</td>
              <td>{{ $order->item_amount }}</td>
              <td>{{ $order->item_total }}</td>
              <td>{{ $order->item_quantity }}</td>
              <td>{{ $order->status }}
                <!---select class="status_change form-control delivery_order_change_status" data-id="{{ $order->id }}">
                  <?php $status_value = array('Accepted','Cancelled','Pending','Hold','Ready for dispatch');
                  foreach ($status_value as $value) {
                          echo '<option value="'.$value.'"'. ($order->status == $value ? "selected" : "").' data_id="'.$order->id.'">'.$value.'</option>';
                      }
                  ?>
                </select--->
              </td>
              <td>
                <div class="dropdown text-center">
                  <a href="#" class="dropdown-toggle arrow-none card-drop" data-toggle="dropdown" aria-expanded="false">
                      <i class="mdi mdi-dots-vertical"></i>
                  </a>
                  <div class="dropdown-menu dropdown-menu-right">
                    <a class="dropdown-item delivery_chage_status" data-toggle="modal" data-target="#brandModal" data-data='<?php echo json_encode($order); ?>'>
                      <i class="fa fa-user-edit"></i> Change status
                    </a>
                    <a class="dropdown-item" href="{{ url('delivery/order/view/'.$order->sub_order_code) }}">
                      <i class="fa fa-eye"></i> View
                    </a>
                    @if($order->status == 'Pending')
                    <!--a class="dropdown-item" href="{{ url('delivery/order/delete/'.$order->id) }}" onclick="return confirm('Are you sure?')">
                      <i class="fas fa-trash"></i> Delete
                    </a-->
                    @endif
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

<!-- Modal -->
<div id="brandModal" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title exp_heading">Update status</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
        <!--begin::Form-->
        {{ Form::open(array('url' => 'delivery/update_status', 'files' => true, 'class' => '','id'=>'form', 'enctype'=>'multipart/form-data')) }}
          <input type="hidden" name="id" id="order_id" value="0">
          <div class="row">
            <div class="col-12 col-md-12">
              <div class="form-group">
                <label>Status</label>
                {{Form::select('status',['Completed'=>'Complete','Cancelled'=>'Cancel'],null,array('class'=>'form-control','id'=>'status1')) }}
                
              </div>
              <div class="form-group">
                <label>Reason</label>
                {{Form::textarea('reason',null,array('class'=>'form-control','id'=>'reason')) }}              
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
  <!-- end row -->
</div>

@endsection
