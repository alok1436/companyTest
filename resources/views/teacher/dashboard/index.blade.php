@extends('delivery.layouts.default')
@section('pageTitle', 'Delivery Boy | Dashboard')
@section('content')

<!-- Start Content-->
<div class="container-fluid">
  <div class="row">
    <div class="col-xl-3 col-md-6">
      <div class="card-box">
        <h4 class="header-title mt-0 mb-4">Total Dispatch</h4>       
            <h2 class="font-weight-normal pt-1 mb-1"><strong>{{ $DispatchOrders }}</strong></h2>
      </div>
    </div>
    <div class="col-xl-3 col-md-6">
      <div class="card-box">
        <h4 class="header-title mt-0 mb-4">Total Delivery</h4>       
            <h2 class="font-weight-normal pt-1 mb-1"><strong>{{ $CompletedOrders }}</strong></h2>
      </div>
    </div>
      <div class="col-xl-3 col-md-6">
      <div class="card-box">
        <h4 class="header-title mt-0 mb-4">Total cancelled</h4>       
            <h2 class="font-weight-normal pt-1 mb-1"><strong>{{ $CancelledOrders }}</strong></h2>
      </div>
    </div>
      <div class="col-xl-3 col-md-6">
      <div class="card-box">
        <h4 class="header-title mt-0 mb-4">Total Earned</h4>       
            <h2 class="font-weight-normal pt-1 mb-1"><strong>{{ number_format($EarnedTotal,2)}}</strong></h2>
      </div>
    </div>
    <!-- end col -->
  </div>
  <!-- end row -->

 <!--  <div class="row">
    <div class="col-xl-6">
    <div class="card-box">
     <div class="dropdown float-right">
          <a href="#" class="dropdown-toggle arrow-none card-drop" data-toggle="dropdown" aria-expanded="false">
            <i class="mdi mdi-dots-vertical"></i>
          </a>
          <div class="dropdown-menu dropdown-menu-right">
             item-->
            <!---<a href="javascript:void(0);" onclick="get_sales_chart('Daily')" class="dropdown-item">Daily</a>
            
            a href="javascript:void(0);" onclick="get_sales_chart('Weekly')" class="dropdown-item">Weekly</a>
            
            <a href="javascript:void(0);" onclick="get_sales_chart('Monthly')" class="dropdown-item">Monthly</a>
            
            <a href="javascript:void(0);" onclick="get_sales_chart('Yearly')" class="dropdown-item">Yearly</a
          </div>
        </div>----->
        <!-- <h4 class="header-title mt-0" id="sales_heading">Daily Sales</h4>        
         <div class="" id="sales_chart"><canvas id="line-chart" width="800" height="450"></canvas></div> -->
        <!---</div>

    </div>

    <div class="col-xl-6">
    
    </div>
     end col -->
  <!--</div> -->
  <!-- end row -->

  <div class="row">
   <div class="col-xl-6">
      <div class="card-box">
        <div class="dropdown float-right">
          <a href="#" class="dropdown-toggle arrow-none card-drop" data-toggle="dropdown" aria-expanded="false">
            <i class="mdi mdi-dots-vertical"></i>
          </a>
        </div>

        <h4 class="header-title mt-0 mb-3">Latest Orders</h4>

        <div class="table-responsive">
          <table class="table table-hover mb-0">
            <thead>
              <tr>
                <th>#</th>
                <th>Product</th>
                <th>User</th>
                <th>Status</th>
                <th>Total</th>
                <!--th>View</th-->
              </tr>
            </thead>
            <tbody>
              <?php $latest_order = array();
              $i=1;
              ?>
            @foreach($orders as $order)
            <?php //dd();?>
              <tr>
                <td>{{ $i }}</td>
                <td>{{$order->item_name}}</td>
                <td> {{ $order->customer->full_name }} </td>
                <td>{{$order->status}}</td>
                <td>{{$order->item_total}}</td>
                <!--td><a class="dropdown-item" href="{{ url('vendor/order/view/'.$order->order_code) }}">
                      <i class="fa fa-eye"></i> View
                    </a></td-->
              </tr>
              <?php $i++; ?>
              @endforeach
            </tbody>
          </table>
           {{ $orders->links() }}

        </div>
      </div>
    </div>
    <!-- end col -->

    <!-- <div class="col-xl-6">
      <div class="card-box">
        <div class="dropdown float-right">
          <a href="#" class="dropdown-toggle arrow-none card-drop" data-toggle="dropdown" aria-expanded="false">
            <i class="mdi mdi-dots-vertical"></i>
          </a>
          <div class="dropdown-menu dropdown-menu-right">
        
          </div>
        </div>

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
            </tbody>
          </table>
        </div>
      </div>
    </div> -->
    <!-- end col -->
  </div>
  <!-- end row -->
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
          $("#sales_chart").html(response.data);
      },
      error:function(status,error){
      }     
  });     
  }
  
  get_sales_chart('Daily');
 
</script>
@endsection