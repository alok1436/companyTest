@extends('vendor.layouts.default')
@section('pageTitle', 'Vendor | Dashboard')
@section('content')
<style type="text/css">
  .highcharts-credits{
    display: none !important;
  }
</style>
<!-- Start Content-->
<div class="container-fluid">
  <div class="row">
    <div class="col-xl-3 col-md-6">
      <div class="card-box">
        <h4 class="header-title mt-0 mb-4">Total Orders</h4>       
            <h2 class="font-weight-normal pt-1 mb-1"><strong>{{ $orders }}</strong></h2>
      </div>
    </div>
    <div class="col-xl-3 col-md-6">
      <div class="card-box">
        <h4 class="header-title mt-0 mb-4">Total Completed Sales</h4>       
            <h2 class="font-weight-normal pt-1 mb-1"><strong>{{number_format($orderSale,2)}}</strong></h2>
      </div>
    </div>
    <div class="col-xl-3 col-md-6">
      <div class="card-box">
        <h4 class="header-title mt-0 mb-4">Total Pending Sales</h4>       
            <h2 class="font-weight-normal pt-1 mb-1"><strong>{{number_format($orderSalePending,2)}}</strong></h2>
      </div>
    </div>  
    <!-- end col -->
  </div>
  <!-- end row -->



 <div class="row">
    <div class="col-xl-3 col-md-3 col-6">
      <div class="card-box">
        <h4 class="header-title mt-0 mb-4">Processing orders</h4>       
            <h2 class="font-weight-normal pt-1 mb-1"><strong>{{ $ProcessingOrders }}</strong></h2>
      </div>
    </div>
    <div class="col-xl-3 col-md-3 col-6">
      <div class="card-box">
        <h4 class="header-title mt-0 mb-4">Pending orders</h4>       
            <h2 class="font-weight-normal pt-1 mb-1"><strong>{{ $PendingOrders }}</strong></h2>
      </div>
    </div>
    <div class="col-xl-3 col-md-3 col-6">
      <div class="card-box">
        <h4 class="header-title mt-0 mb-4">Cancelled Orders</h4>       
            <h2 class="font-weight-normal pt-1 mb-1"><strong>{{ $CancelOrders }}</strong></h2>
      </div>
    </div>
     <div class="col-xl-3 col-md-3 col-6">
      <div class="card-box">
        <h4 class="header-title mt-0 mb-4">Low stock products </h4>       
            <h2 class="font-weight-normal pt-1 mb-1"><strong>{{ $lowStockProductCount }}</strong></h2>
      </div>
    </div>

    
    <!-- end col -->
  </div>
  <!-- end row -->
  
  <div class="row">
    <!--- Daily sales chart start--->
    <div class="col-xl-6">
    <div class="card-box" style="height:450px;">
    <div class="dropdown float-right">
          <a href="#" class="dropdown-toggle arrow-none card-drop" data-toggle="dropdown" aria-expanded="false">
            <i class="mdi mdi-dots-vertical"></i>
          </a>
          <div class="dropdown-menu dropdown-menu-right">
            <!-- item-->
            <a href="javascript:void(0);" onclick="get_sales_chart('Daily')" class="dropdown-item chartHeading">Daily</a>
            
            <a href="javascript:void(0);" onclick="get_sales_chart('Weekly')" class="dropdown-item chartHeading">Weekly</a>
            
            <a href="javascript:void(0);" onclick="get_sales_chart('Monthly')" class="dropdown-item chartHeading">Monthly</a>
            
            <a href="javascript:void(0);" onclick="get_sales_chart('Yearly')" class="dropdown-item chartHeading">Yearly</a>
          </div>
    </div>
    <h4 class="header-title mt-0" id="sales_heading">Weekly Sales</h4>        
    <div class="" id="sales_chart"><div id="view-chart" width="800" height="450"></div></div>
    </div>

    </div>
    <!--- Daily sales chart end--->

    <!--- Daily sales count chart start--->
    <div class="col-xl-6">

      <div class="card-box" style="height:450px;">
        <div class="dropdown float-right">
              <a href="#" class="dropdown-toggle arrow-none card-drop" data-toggle="dropdown" aria-expanded="false">
                <i class="mdi mdi-dots-vertical"></i>
              </a>
              <div class="dropdown-menu dropdown-menu-right">
                <!-- item-->
                <a href="javascript:void(0);" onclick="get_sales_chart_count('Daily')" class="dropdown-item chartHeadingCount">Daily</a>
                
                <a href="javascript:void(0);" onclick="get_sales_chart_count('Weekly')" class="dropdown-item chartHeadingCount">Weekly</a>
                
                <a href="javascript:void(0);" onclick="get_sales_chart_count('Monthly')" class="dropdown-item chartHeadingCount">Monthly</a>
                
                <a href="javascript:void(0);" onclick="get_sales_chart_count('Yearly')" class="dropdown-item chartHeadingCount">Yearly</a>
              </div>
        </div>
        <h4 class="header-title mt-0" id="sales_heading_count">Daily Orders</h4>        
        <div class="" id="sales_chart_count"><div id="view-chart-count" width="800" height="300"></div></div>
      </div>

    </div>
    <!--- Daily sales count chart end--->
    
  </div>
  <!-- end row -->

  <div class="row">
   <div class="col-xl-12">
      <div class="card-box">
        <h4 class="header-title mt-0 mb-3">Latest Orders</h4>
        <div class="table-responsive">
          <table class="table table-hover mb-0">
            <thead>
              <tr>
                <th>#</th>
                <th>Product</th>
                <th>User</th>
                <th>Total</th>
                <th>View</th>
              </tr>
            </thead>
            <tbody>
            <?php $j = 1;?>
            @foreach($latest_order as $order)
              <tr>
                <td>{{ $j }}</td>
                <td>{{$order->item_name}}</td>
                <td> {{ $order->customer ? $order->customer->first_name : '' }} </td>
                <td>{{$order->item_total}}</td>
                <td>
                  <a class="dropdown-item" href="{{ url('vendor/order/view/'.$order->vendor_id.'/'.$order->order_code) }}">
                    <i class="fa fa-eye"></i> View
                  </a>
                </td>
              </tr>
              <?php $j++; ?>
            @endforeach
            </tbody>
          </table>
        </div>
      </div>
    </div>
    <!-- end col -->

    <div class="col-xl-12">
      <div class="card-box">
        <h4 class="header-title mt-0 mb-3">Top Products</h4>
        <div class="table-responsive">
          <table class="table table-hover mb-0">
            <thead>
              <tr>
                <th>#</th>
                <th>Product</th>
                <th>Vendor</th>
                <th>Item Total</th>
                <th>Net Sales</th>
              </tr>
            </thead>
            <tbody> 
            <?php $i=1; ?> 
              @foreach($products as $product)
                @if($product->stock)
                <?php $orderItem = App\Order::with('item')->where(['item_id'=>$product->id,'vendor_id'=>$product->author->id])->get()->sum('item_quantity'); ?>
                  <tr>
                    <td>{{ $i }}</td>
                    <td>{{ $product->name }}</td>
                    <td>{{ $product->author->first_name.' '.$product->author->last_name }}</td>
                    <td>{{ $product->stock }}</td>
                    <td>{{ $orderItem }}</td>
                  </tr>
                  <?php $i++; ?>
                @endif
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
    </div>
    <!-- end col -->
  </div>
  <!-- end row -->
  <div class="row">
     <div class="col-xl-12">
      <div class="card-box">
        <h4 class="header-title mt-0 mb-3">Low stock products</h4>
        <div class="table-responsive">
          <table class="table table-hover mb-0">
            <thead>
              <tr>
                <th>#</th>
                <th>Product</th>
                <th>Vendor</th>
                <th>Stock</th>
                <th>Net Sales</th>
              </tr>
            </thead>
            <tbody> 
            <?php $i=1; ?> 
              @foreach($lowStockProducts as $lowStockProduct)
                @if($lowStockProduct->stock)
                <?php $orderItem = App\Order::with('item')->where(['item_id'=>$lowStockProduct->id,'vendor_id'=>$lowStockProduct->author->id])->get()->sum('item_quantity'); ?>
                  <tr>
                    <td>{{ $i }}</td>
                    <td>{{ $lowStockProduct->name }}</td>
                    <td>{{ $lowStockProduct->author->first_name.' '.$lowStockProduct->author->last_name }}</td>
                    <td>{{ $lowStockProduct->stock }}</td>
                    <td>{{ $orderItem }}</td>
                  </tr>
                  <?php $i++; ?>
                @endif
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
<!-- container -->
<script src="{{ asset('backend/js/jquery-3.2.1.min.js') }}"></script>
<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.5.0/Chart.min.js"></script>
<script>

  function get_sales_chart(type){
      $.ajax({  
      url: "{{ url('vendor/getsalesReport') }}",
      data: {
                _token: "{{ csrf_token() }}",
        type: type
      },
      type: 'POST',
      success:function(response){
          console.log(response);
          $("#sales_chart").html(response.data);

      },
      error:function(status,error){
      }     
  });     
  }
  get_sales_chart('Weekly');

  //get sales count
   function get_sales_chart_count(type){
      $.ajax({  
      url: "{{ url('vendor/getsalesReportCount') }}",
      data: {
        _token: "{{ csrf_token() }}",
        type: type
      },
      type: 'POST',
      success:function(response){
          console.log(response);
          $("#sales_chart_count").html(response.data);

      },
      error:function(status,error){
      }     
  });     
  }
  get_sales_chart_count('Weekly');
  
  jQuery(document).ready(function(){
    jQuery('.chartHeading').click(function(){
      var chartHeadingText = jQuery(this).text();
      var finlyHeadingText = chartHeadingText+ " Sales";
      jQuery('#sales_heading').text(' ');
      jQuery('#sales_heading').text(finlyHeadingText);
    });

    jQuery('.chartHeadingCount').click(function(){
      var chartHeadingText = jQuery(this).text();
      var finlyHeadingText = chartHeadingText+ " Orders";
      jQuery('#sales_heading_count').text(' ');
      jQuery('#sales_heading_count').text(finlyHeadingText);
    });

  });
</script>
@endsection