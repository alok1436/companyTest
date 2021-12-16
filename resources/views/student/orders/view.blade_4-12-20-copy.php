@extends('vendor.layouts.default')
@section('pageTitle', 'Vendor | View Order') 
@section('content')

<!-- Start Content-->
<div class="container-fluid">
 
<!-- Modal -->
<div id="attributes_container_motal" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title exp_heading">Attributes</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body" id="attributes_container">
        
      </div>
    </div>
  </div>
</div>
<div id="options_container_modal" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title exp_heading">Order options</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body" id="options_container">
        
      </div>
    </div>
  </div>
</div>
    <div class="row">
        <div class="col-md-12">
            <div class="card-box">
                <!-- <div class="panel-heading">
                    <h4>Invoice</h4>
                </div> -->
                <div class="panel-body">
                   <h3 class="text-center heading">View Order</h3>
                    <div class="clearfix">
                        <div class="float-left">
                            <img src="{{ asset('backend/assets/images/logo-dark.png') }}" alt="" height="50" />
                        </div>
                        <div class="float-right print_btn_ct">
                            <a href="javascript:window.print()" class="btn btn-dark waves-effect waves-light"><i class="fa fa-print"></i></a>
                        </div>
                    </div> 
                    <hr>
                    <div class="row">
                        <div class="col-md-3">
                                <strong>Vendor Detail</strong><br>
                                <?php //dd($order->vendor); ?>
                                <div>Name : <span>{{ Auth::user()->full_name }}</span></div>
                                <div>Email : <span>{{ Auth::user()->email }}</span></div>
                                <div>Phone : <span>{{ Auth::user()->phone }}</span></div>
                                <div>Address : <span>{{ Auth::user()->address }}</span></div>   
                                <br>
                                <strong>Customer Detail</strong><br>
                                
                                <div>Name : <span>{{ $order->customer->full_name }}</span></div>
                                <div>Email : <span>{{ $order->customer->email  }}</span></div>
                                <div>Phone : <span>{{ $order->customer->phone  }}</span></div>   
                          </div>
                          <div class="col-md-3">    
                                <strong>Billing Address</strong><br>
                                @if($order->customer)
                                  <?php $customerMeta = $order->customer->getMeta(); ?>
                                  @if(isset($customerMeta['billing_address'][0]))
                                    @foreach($customerMeta['billing_address'][0] as $key=>$value)
                                      @if($key && $value)
                                        <div><strong><?php echo ucfirst(str_replace('_',' ',$key)) ?> </strong>: <span><?php echo $value ?></span></div>      
                                      @endif 
                                    @endforeach
                                  @endif             
                                @endif             
                          </div>
                          <div class="col-md-3">
                              <strong>Shipping Address</strong><br>
                              @if($order->customer) 
                                @if(isset($customerMeta['shipping_address'][0]))
                                  @foreach($customerMeta['shipping_address'][0] as $key=>$value)
                                    @if($key && $value)
                                      <div><strong><?php echo ucfirst(str_replace('_',' ',$key)) ?> </strong>: <span><?php echo $value ?></span></div> 
                                    @endif      
                                  @endforeach
                                @endif
                              @endif
                        </div><!-- end col -->
                          <div class="col-md-3">
                             <p><strong>Order Date: </strong> {{ $order->created_at->format('d F, Y H:i A') }}</p>
                             <p><strong>Payment status: </strong> {{ $order->payment_status }}</p>
                        </div><!-- end col -->
                    </div>
                    <!-- end row -->
                    <div class="row">
                        <div class="col-md-12">
                            <div class="table-responsive orderlist">
                                <table class="table mt-4">
                                    <thead>
                                      <tr>
                                        <tr>
                                        <th>Image</th>
                                        <th>Order Code</th>
                                        <th>Name</th>
                                        <th>Payment Status</th>
                                        <th>Payment Method</th>
                                        <th>Quantity</th>
                                        <th>Amount</th>
                                        <th class="text-right">Total</th>
                                        <th>Commission</th>
                                        <th class="text-right">Attributes</th>
                                        <th>Status</th>
                                      </tr>
                                      </tr>
                                    </thead>
                                    <tbody>
                                      <?php $total = 0; ?>
                                      @foreach($sub_orders as $sub_order)
                                      <?php $meta = $sub_order->getMeta(); ?>
                                      <?php $total = $total + $sub_order->item_amount;  ?>
                                      <tr>
                                        <td>
                                          <a class="example-image-link" href="{{ isset($meta['item_data']['attatchments']['media']['full']) ? $meta['item_data']['attatchments']['media']['full'] : '' }}" data-lightbox="example-set">
                                          <img src="{{ isset($meta['item_data']['attatchments']['media']['thumbnail']) ? $meta['item_data']['attatchments']['media']['thumbnail'] : '' }}" width="100px">
                                          </a>
                                        </td>
                                        <td>{{ $sub_order->sub_order_code }}</td>
                                        
                                        
                                        <td>
                                            {{ $sub_order->item_name }}<br>
                                            {{ $sub_order->getorderattributes() }}<br>
                                            {{ $sub_order->vendor ? 'Sold by: '.$sub_order->vendor->full_name : '' }}
                                        </td>

                                        <td>{{ $sub_order->payment_status }}</td>
                                        <td>{{ $sub_order->payment_method }}</td>
                                        <td>{{ $sub_order->item_quantity }}</td>
                                        <td>{{ $sub_order->item_amount }}</td>
                                        <td class="text-right">{{ $sub_order->item_total }}</td>
                                        <td>{{ $sub_order->commission }}</td>
                                        <td>{{ $sub_order->status }}</td>
                                        <td>
                                            <div class="dropdown">
                                                <a href="#" class="dropdown-toggle arrow-none card-drop" data-toggle="dropdown" aria-expanded="false">
                                                  <i class="mdi mdi-dots-vertical"></i>
                                                </a>
                                                <div class="dropdown-menu dropdown-menu-right">
                                                  <!-- item-->
                                                  <!--a href="javascript:void(0);" class="show_attributes dropdown-item" id="btn_{{$sub_order->id}}" data-id="{{$sub_order->id}}" >View attribute</a-->

                                                  <a href="javascript:void(0);" class="get_options dropdown-item" id="btn_update_order_{{$sub_order->id}}" data-id="{{$sub_order->id}}" >Update order</a>
                                                  
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
                    <div class="row">
                        <div class="col-xl-4 col-6">     
                        </div>                   
                        <div class="col-xl-8 col-6" style="margin-top: 30px;">  
                            <p class="text-right" style="margin-right: 14px;"><b>Sub total :</b> {{ number_format($total,2) }} </p>
                            <p class="text-right" style="margin-right: 14px;"><b>Grand Total :</b> {{ number_format($total,2) }} </p>
                        </div>
                    </div>    
                </div>
            </div>
        </div>
    </div>
    <!-- end row -->        
 
<!-- Modal -->
<div id="updateStatusModal" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title exp_heading">Update status</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
        <!--begin::Form-->
        {{ Form::open(array('url' => 'vendor/update_status', 'files' => true, 'class' => '','id'=>'form', 'enctype'=>'multipart/form-data')) }}
          <input type="hidden" name="id" id="order_id" value="0">
          <div class="row">
            <div class="col-12 col-md-12">
              <div class="form-group">
                <label>Status</label>
                {{Form::select('status',['Accepted'=>'Accepted','Ready for dispatch'=>'Ready for dispatch','Cancelled'=>'Cancelled'],null,array('class'=>'form-control','id'=>'status1')) }}
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
</div> <!-- container-fluid -->
<style type="text/css">
 @media print {
     .table-responsive.orderlist td:last-child {display:none}
     .table-responsive.orderlist th:last-child {display:none}
     .print_btn_ct {display:none}
 }

 h3.heading {
    background-color: #ccc;
    font-size: 1.2rem;
    letter-spacing: 0px;
    padding: 2px;
}
.table thead th {
    vertical-align: bottom;
    border-bottom: 1px solid #000;
    border-top: 1px solid #000;
    background-color: #000;
    color: #fff;
}
.table td, .table th {
    border: 1px solid #000;
}
</style>
@endsection
