@extends('delivery.layouts.default')
@section('pageTitle', 'Delivey | View Order') 
@section('content')

<!-- Start Content-->
<div class="container-fluid">

    <div class="row">
        <div class="col-md-12">
            <div class="card-box">
                <!-- <div class="panel-heading">
                    <h4>Invoice</h4>
                </div> -->
                <div class="panel-body">
                    <div class="clearfix">
                        <div class="float-left">
                            <img src="{{ asset('backend/assets/images/logo-dark.png') }}" alt="" height="50" />
                        </div>
                        <div class="float-right">
                            <a href="javascript:window.print()" class="btn btn-dark waves-effect waves-light"><i class="fa fa-print"></i></a>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-md-3">
                                <strong>Vendor Detail</strong><br>
                                <div>Name : <span>{{ $order ? $order->vendor->full_name : 'N/A' }}</span></div>
                                <div>Email : <span>{{ $order ? $order->vendor->email : 'N/A' }}</span></div>
                                <div>Phone : <span>{{ $order ? $order->vendor->phone : 'N/A' }}</span></div>
                                <div>Address : <span>{{ $order ? $order->vendor->getMeta()['address'] : 'N/A' }}</span></div>    
                          </div>

                          <!--div class="col-md-3">    
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
                          </div-->
                          <div class="col-md-6">
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
                             <p><strong>Order Date: </strong> {{ $order->created_at->format('d-m-Y H:i:s A') }}</p>
                             <p><strong>Payment method: </strong> {{ $order->payment_method }}</p>
                             <!--p><strong>Payment status: </strong> {{ $order->payment_status }}</p-->
                        </div><!-- end col -->
                    </div>
                    <!-- end row -->

                    <div class="row">
                        <div class="col-md-12">
                            <div class="table-responsive">
                                <table class="table mt-4">
                                    <thead>
                                      <tr><th>#</th>
                                          <th>Image</th>
                                          <th>Order Code</th>
                                          <th>Name</th>
                                          <th>Status</th>
                                          <th>Payment Method</th>
                                          <th>Quantity</th>
                                          <th>Amount</th>
                                          <th class="text-right">Total</th>
                                      </tr>
                                    </thead>
                                      <tbody>
                                      <tr> 
                                          <td>1</td>
                                          <td>
                                          <?php $meta = $order->getMeta(); //dd($meta); ?>
                                          <a class="example-image-link" href="{{ isset($meta['item_data']['attatchments']['media']['full']) ? $meta['item_data']['attatchments']['media']['full'] : '' }}" data-lightbox="example-set">
                                            <img src="{{ isset($meta['item_data']['attatchments']['media']['thumbnail']) ? $meta['item_data']['attatchments']['media']['thumbnail'] : '' }}" width="50px">
                                          </a>
                                        </td>
                                          <td>{{ $order->sub_order_code }}</td>
                                          <td>{{ $order->item_name }}</td>
                                          <td>{{ $order->status }}</td>
                                          <td>{{ $order->payment_method }}</td>
                                          <td>{{ $order->item_quantity }}</td>
                                          <td>{{ $order->item_amount }}</td>
                                          <td class="text-right">{{ $order->item_total }}</td>
                                      </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xl-6 col-6">
                            <div class="clearfix mt-4">
                                <!--h5 class="small text-dark">PAYMENT TERMS AND POLICIES</h5>
                                <small>
                                    All accounts are to be paid within 7 days from receipt of
                                    invoice. To be paid by cheque or credit card or direct payment
                                    online. If account is not paid within 7 days the credits details
                                    supplied as confirmation of work undertaken will be charged the
                                    agreed quoted fee noted above.
                                </small-->
                            </div>
                        </div>
                        <div class="col-xl-3 col-6 offset-xl-3">
                            <p class="text-right" style="margin-right: 14px;"><b>Sub total :</b> {{ $order->item_total }} </p>
                            <!--p class="text-right">Discout: 12.9%</p>
                            <p class="text-right">VAT: 12.9%</p>
                            <hr>
                            <h3 class="text-right">USD 2930.00</h3-->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- end row -->        
    
</div> <!-- container-fluid -->

@endsection
