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
                    <div class="clearfix">
                        <div class="float-left">
                            <img src="{{ asset('backend/assets/images/logo-dark.png') }}" alt="" height="100" />
                        </div>
                        <div class="float-right">
                            <h4 style="margin1: 0">Klothus nepal</h4>
                                Dillibazar<br>
                                Kathmandu, Nepal<br>
                        </div>
                    </div> 
                    <?php /*
                    <div class="row">
                        <div class="col-md-12">
                            <div class="float-left mt-3">
                                <address>
                                    <strong>Ship to<br>
                                      {{ $order->customer ? $order->customer->full_name : 'N/A' }}<br>
                                    <?php echo $order->getFormattedBillingAddress() ?>
                                     </strong>
                                </address>
                            </div>
                            <div class="float-right mt-3">
                            <p>
                                <strong>Order No.: </strong> {{ $order->order_code }}<br> 
                                <strong>Order Date: </strong> {{ $order->created_at->format('d/m/Y H:i A') }}<br> 
                                <strong>Ship date: </strong> {{ \Carbon\Carbon::now()->format('d/m/Y H:i A') }}<br> 
                            </p> 
                            </div>
                            <div class="float-right print_btn_ct">
                            <!--a href="javascript:window.print()" class="btn btn-dark waves-effect waves-light"><i class="fa fa-print"></i></a-->
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                          <div class="col-md-3">
                              <strong>Customer Detail</strong><br>
                              <div>Name : <span>{{ $order->customer->full_name }}</span></div>
                              <div>Email : <span>{{ $order->customer->email  }}</span></div>
                              <div>Phone : <span>{{ $order->customer->phone  }}</span></div>   
                          </div>
                          <div class="col-md-3">   
                                <strong>Billing Address</strong><br>
                                <?php echo $order->getFormattedBillingAddress(true) ?>          
                          </div>
                          <div class="col-md-3">
                                <strong>Shipping Address</strong><br>
                                <?php echo $order->getFormattedShippingAddress(true) ?>
                          </div><!-- end col -->
                          <div class="col-md-3">
                             <p><strong>Order Date: </strong> {{ $order->created_at->format('d F, Y H:i A') }}</p>
                             <p><strong>Payment status: </strong> {{ $order->payment_status }}</p>
                        </div><!-- end col -->
                    </div>
                    */ ?>
                    <!-- end row -->            
                    <div class="row">
                        <div class="col-md-12">
                            <div class="table-responsive orderlist">
                                <table class="table mt-4">
                                    <thead>
                                      <tr>
                                        <th>S/N</th>
                                        <th>Image</th>
                                        <th>Item ID</th>
                                        <th>Description</th>
                                        <th>Item SKU</th>
                                        <th>Qty</th>
                                        <th>Unit price</th>
                                        <th>Total price</th>
                                        <th>Status</th>
                                      </tr>
                                    </thead>
                                    <tbody>
                                      <?php $total = 0; ?>
                                      @foreach($sub_orders as $key=>$sub_order)
                                      <?php $meta = $sub_order->getMeta(); ?>
                                      <tr>
                                       <td>{{ $key+1 }}</td>
                                       <td>
                                          <a class="example-image-link" href="{{ isset($meta['item_data']['attatchments']['media']['full']) ? $meta['item_data']['attatchments']['media']['full'] : '' }}" data-lightbox="example-set">
                                            <img src="{{ isset($meta['item_data']['attatchments']['media']['thumbnail']) ? $meta['item_data']['attatchments']['media']['thumbnail'] : '' }}" width="50px">
                                          </a>
                                        </td>
                                        <td>{{ $sub_order->sub_order_code }}</td>
                                        <td>
                                           {{ $sub_order->item_name }}<br>
                                           {{ $sub_order->getorderattributes() }}<br>
                                            {{ $sub_order->vendor ? 'Sold by: '.$sub_order->vendor->full_name : '' }}
                                            @if($sub_order->getMeta('reason') !='')
                                            <br>
                                              {{$sub_order->getMeta('reason')}}
                                            @endif
                                          </td>
                                        <td>
                                            @if($sub_order->item)
                                            @if($sub_order->item->product_type == 'single')
                                                {{ $sub_order->item->skus->sku }}
                                            @else
                                                <?php $variation = $sub_order->item->skusVary->where('id',$sub_order->variations['id'])->first(); ?>
                                                {{ $variation->sku }}
                                            @endif
                                          @else
                                            
                                          @endif
                                        </td>
                                        <td>{{ $sub_order->item_quantity }}</td>
                                        <td>Rs. {{ $sub_order->item_amount }}</td>
                                        <td>Rs. {{ $sub_order->item_total }} <?php $total += $sub_order->item_total?></td>
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
                                      <tr>
                                        <td colspan="6" class="text-right">&nbsp;</td>
                                        <td class="text-center">Total</td>
                                        <td colspan="">Rs. {{ number_format($total, 2, '.', '') }}</td>
                                        <td></td>
                                      </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xl-8 col-6">
                        </div>
                        <div class="col-xl-4 col-6">
                            <div class="row">
                                <div class="col-md-9 text-right">Total Unit Price</div>
                                <div class="col-md-3 col-6 text-right price_ct"><span class="abc">Rs.</span><span class="abc2">{{ number_format($order->subtotal,2, '.', '') }}</span>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-9 text-right">Total Shipping</div>
                                <div class="col-md-3 col-6 text-right price_ct" style="border-bottom: 1px solid #000;"><span class="abc">Rs.</span><span class="abc2">{{ number_format($order->shipping,2, '.', '') }}</span>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-9 text-right">Total</div>
                                <div class="col-md-3 col-6 text-right price_ct"><span class="abc">Rs.</span><span class="abc2">{{ number_format($order->shipping+$order->subtotal,2, '.', '')  }}</span>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-9 text-right">Total Discount</div>
                                <div class="col-md-3 col-6 text-right price_ct" style="border-bottom: 2px solid #000;"><span class="abc">Rs.</span><span class="abc2">{{ number_format($order->discount,2, '.', '')  }}</span>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-9 text-right">Total Payable Amount</div>
                                <div class="col-md-3 col-6 text-right price_ct" style="border-bottom: 2px solid #000;"><span class="abc">Rs.</span><span class="abc2">{{ number_format(($order->subtotal+$order->shipping)-$order->discount,2, '.', '') }}</span>
                                </div>
                            </div>
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
    body {
      -webkit-print-color-adjust: exact !important;
      background-color: #fff;
    }
    .content-page {
      background: #fff;
    }
    .footer {
      background: #fff;
    }
   .table-responsive.orderlist td:last-child {display:none}
   .table-responsive.orderlist th:last-child {display:none}
   .print_btn_ct {display:none}
   .table thead th {
      vertical-align: bottom;
      border-bottom: 2px solid #dee2e6;
      background-color: #000 !important;
      color:#fff !important;
    }
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
