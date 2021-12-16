@extends('vendor.layouts.default')
@section('pageTitle', 'Vendor | Orders') 
@section('content')

<div class="container-fluid">
  <div class="row">
    <div class="col-12">
      <div class="card-box">
        <form class="form-inline" action="" method="get">
          <div class="custformwrap">
            <label class="sr-only12" for="order_code">Order or sub order code</label>
            <input type="text" class="form-control12 form-control mb-2 mr-sm-2" name="sub_order_code" placeholder="Order or sub order code" value="{{request()->sub_order_code }}">
          </div>
          <div class="custformwrap">
            <label class="sr-only12" for="order_code">From date</label>
            <input id="datepicker1" type="text" class="form-control mb-2 mr-sm-2" name="from_date" placeholder="DD/MM/YYYY" value="{{request()->from_date }}" format="YYYY-MM-DD">
          </div>
          <div class="custformwrap">
            <label class="sr-only12" for="order_code">To date</label>
            <input id="datepicker2" type="text" class="form-control mb-2 mr-sm-2" name="to_date" placeholder="DD/MM/YYYY" value="{{request()->to_date }}" format="YYYY-MM-DD">
          </div>
          <div class="custformwrap">
          <label class="sr-only12" for="order_status">Status</label>
          {!! csrf_field() !!}
            <select type="text" class="form-control mb-2 mr-sm-2" name="status" id="order_status" placeholder="Enter order code">
              <?php $status_value = ['Select status','Pending','Accepted','Ready for dispatch','Cancelled'];
                  foreach ($status_value as $value) {
                      echo '<option value="'.$value.'"'. (ucfirst(request()->status) == $value ||  request()->order_status == $value ? "selected" : "").'>'.$value.'</option>';
                  }
                ?>
            </select>
          </div>
          <?php //dd(request()->all()); ?>
          <div class="custformwrap">
            <label class="sr-only12" for="order_status">&nbsp;</label>
            <button type="submit" name="action" value="submit_filter" name="true" class="btn btn-primary mb-2">Filter</button>
            @if(\Request::get('action') == 'submit_filter')
            &nbsp;<a href="" onclick="location.reload()" class="btn btn-warning mb-2">Clear filter</a>
            @endif
            &nbsp;<a href="{{ request()->has('action') ? request()->fullUrl().'&export=1' : request()->fullUrl().'?export=1'  }}" class="btn btn-success mb-2">Export</a>
            </div>
        </form>
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

        <table id="datatable1" class="table">
          <thead>
            <tr>
              <th>#</th>
              <th>Item name</th>
              <th>Order Code</th>
              <th>Customer name</th>
              <th>Total</th>
              <th>Commission</th>
              <th>Status</th>
              <th>Date</th>
              <!--th>Status</th--->  
              <th class="text-center">Action</th>
            </tr>
          </thead>
          <tbody>
            <?php $i=1; ?>
            @foreach($orders as $order)
            <tr>
              <td>{{ $i++ }}</td>
              <td>{{ $order->item_name }}</td>
              <td>{{ $order->order_code }}</td>
              <td>{{ $order->customer ? $order->customer->full_name : 'N/A' }}</td>
              <td>{{ number_format($order->getVendorOrdersTotal(),2) }}</td>
              <td>{{ $order->commission }}</td>
              <td>{{ $order->status }}</td>
              <td>{{ $order->created_at->format('d/m/Y H:i A') }}</td>
              <!---td>
                @if($order->status == 'Completed')
                  <span class="badge badge-success badge-pill">{{ $order->status }}</span>
                @else
                <select class="status_change form-control admin_order_change_status" data-id="{{ $order->id }}">
                  <?php $status_value = array('Accepted','Cancelled','Pending','Hold','Ready for dispatch','Processing','Completed','Dispatch');
                  foreach ($status_value as $value) {
                          echo '<option value="'.$value.'"'. ($order->status == $value ? "selected" : "").' data_id="'.$order->id.'">'.$value.'</option>';
                      }
                  ?>
                </select>
                @endif
              </td---->
              <td>
                <div class="dropdown text-center">
                  <a href="#" class="dropdown-toggle arrow-none card-drop" data-toggle="dropdown" aria-expanded="false">
                      <i class="mdi mdi-dots-vertical"></i>
                  </a>
                  <div class="dropdown-menu dropdown-menu-right">
                   <!--  <a class="dropdown-item" href="{{ url('vendor/order/edit/'.$order->id) }}">
                      <i class="fa fa-user-edit"></i> Edit
                    </a> -->

                    <a class="dropdown-item" href="{{ url('vendor/order/view/'.$order->vendor_id.'/'.$order->order_code) }}">
                      <i class="fa fa-eye"></i> View
                    </a>
                  </div>
                </div>
              </td>
            </tr>
            @endforeach
          </tbody>
        </table>
        <div class="pagination_wrapper">
          {{ $orders->links() }}
        </div>
      </div>
    </div>
  </div> 
  <!-- end row -->
</div>
<style type="text/css">
  div#datatable_paginate {
    display: none;
}
div#datatable_info {
    display: none;
}
</style>
@endsection
