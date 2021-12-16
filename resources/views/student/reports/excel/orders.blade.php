<table>
<thead>
  <tr>
    <th>#</th>
    <th>Item name</th>
    <th>Order Code</th>
    <th>Customer name</th>
    <th>Total</th>
    <th>Status</th>
    <th>Date</th>
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
    <td>{{ $order->status }}</td>
    <td>{{ $order->created_at->format('d/m/Y H:i A') }}</td>
  </tr>
  @endforeach
</tbody>
</table>