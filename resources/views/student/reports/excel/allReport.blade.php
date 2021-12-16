<table>
<thead>
  <tr>
    <th>Date/Time</th>
    <th>Order number</th>
    <th>Sub order number</th>
    <th>Quantity</th>
    <th>Item name</th>
    <th>Subtotal</th>
    <th>Total</th>
    <th>Regular price</th>
    <th>Sale price</th>
    <th>Discount</th>
    <th>Commission</th>
    <th>Status</th>
    <th>Customer name</th>
    <th>Customer Address</th>
    <th>Customer Phone</th>
  </tr>
</thead>
<tbody>
  <?php $i=1; ?>
  @foreach($orders as $order)
   <?php $i=1; ?>
  <tr>
    <td>{{ $order->created_at->format('d/m/Y H:i A') }}</td>
    <td>{{ $order->order_code }}</td>
    <td>{{ $order->sub_order_code }}</td>
    <td>{{ $order->item_quantity }}</td>
    <td>{{ $order->item_name }}</td>
    <td>{{ $order->item_total }}</td>
    <td>{{ $order->item_total }}</td>
    <td>
      @if($order->item)
        @if($order->item->product_type == 'single')
            {{ $order->item->skus->regular_price }}
        @else
            <?php $variation = $order->item->skusVary->where('id',$order->variations['id'])->first(); ?>
            {{ $variation ? $variation->regular_price : 0 }}
        @endif
      @else
        0.00
      @endif
    </td>
    <td>
      @if($order->item)
        @if($order->item->product_type == 'single')
            {{ $order->item->skus->sale_price ? $order->item->skus->sale_price : '0.00' }}
        @else
            <?php $variation = $order->item->skusVary->where('id',$order->variations['id'])->first(); ?>
            {{ $variation ? $variation->sale_price : 0 }}
        @endif
      @else
        0.00
      @endif
    </td>
    <td>{{ $order->discount }}</td>
    <td>{{ $order->commission }}</td>
    <td>{{ $order->status }}</td>
    <td>{{ $order->customer ? $order->customer->full_name : '' }}</td>
    <td>{{ $order->customer ? $order->customer->address : '' }}</td>
    <td>{{ $order->customer ? $order->customer->phone : '' }}</td>  
  </tr>
  @endforeach
</tbody>
</table>