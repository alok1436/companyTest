@extends('vendor.layouts.default')
@section('pageTitle', 'Vendor | Orders') 
@section('content')
<div class="container-fluid">
  <div class="row">
    <div class="col-12">
      <div class="card-box">
        <form class="form-inline" action="" method="get">
          <div class="custformwrap">
            <label class="sr-only12" for="order_code">From date</label>
            <input id="datepicker1" type="text" class="form-control mb-2 mr-sm-2" name="from_date" placeholder="DD/MM/YYYY" value="{{request()->from_date }}" format="yyyy-mm-dd">
          </div>
          <div class="custformwrap">
            <label class="sr-only12" for="order_code">To date</label>
            <input id="datepicker2" type="text" class="form-control mb-2 mr-sm-2" name="to_date" placeholder="DD/MM/YYYY" value="{{request()->to_date }}" format="yyyy-mm-dd">
          </div>
          <div class="custformwrap">
            <label class="sr-only12" for="order_status">Status</label>
            {!! csrf_field() !!}
            <select type="text" class="form-control mb-2 mr-sm-2" name="status" id="order_status" placeholder="Enter order code">
              <?php $status_value = ['Select status','Pending','Accepted','Ready for dispatch','Cancelled','Completed'];
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
          &nbsp;<a href="{{ url('admin/reports') }}" onclick="location.reload()" class="btn btn-warning mb-2">Clear filter</a>
         
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

        <table id="datatable" class="table">
          <thead>
            <tr>
              <th>#</th>
              <th>Item name</th>
              <th>Total Sold</th>
              <th>Item amount</th>
              <th>Net Sale</th> 
            </tr>
          </thead>
          <tbody>
            <?php $i=1; 
            $net_sales_total = 0;
            $net_tota_sold = 0;
            $total_sold = 0;
            ?>
            @if($orders->count() > 0)
            @foreach($orders as $order)
            <?php 
              $net_sales = $order->total_sales*$order->item_amount; 
              $net_sales_total += $net_sales;
              $net_tota_sold += $order->item_amount;
              $total_sold +=$order->total_sales;
            ?>
            <tr>
              <td>{{ $i++ }}</td>
              <td>{{ $order->item_name }}</td>
              <td>{{ $order->total_sales }}</td>
              <td>{{ $order->item_amount }}</td>
              <td>{{ number_format($net_sales,2) }}</td>
            </tr>
            @endforeach
            @else
              <tr><td class="text-center" colspan="5">No data available</td></tr>
            @endif
          </tbody>
          <tfoot>
            <tr>
              <td></td>
              <td>Total</td>
              <td>{{ $orders->sum('total_sales') }}</td>
              <td colspan="">{{ number_format($net_tota_sold,2) }}</td>
              <td>{{ number_format($net_sales_total,2) }}</td>
            </tr>
          </tfoot>
        </table>
        <div class="pagination_wrapper">
         v
        </div>
      </div>
    </div>
  </div> 
  <!-- end row -->
</div>
<!-- <style type="text/css">
  div#datatable_paginate {
    display: none;
}
div#datatable_info {
    display: none;
}
</style> -->
@endsection
