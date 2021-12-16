Hi, {{ $order[0]->customer->full_name }}
<h3>Good news! We have successfully received your order.</h3>
<h4>Please find the summary of your order below:</h4>

<div class="" style="width: 60%; margin:0px auto;">
	<table style="width:100%" border="1px solid #ccc">
	  <tr>
	    <th>Item name</th>
	    <th>Quantity</th> 
	    <th>Price</th>
	    <th>Status</th>
	  </tr>
	  @foreach($order as $item)
	  <tr style="text-align: center;">
	    <td>{{ $item->item_name }}</td>
	    <td>{{ $item->item_quantity }}</td>
	    <td>{{ $item->item_amount }}</td>
	    <td>{{ $item->status }}</td>
	  </tr>
	  @endforeach
	</table>
</div>
<p>Thank you for shopping with Klothus. We greatly value your trust and confidence.</p>
<p>Thank You</p>
<p>Klothus.com</p>
<p>Customer Care : 014813643</p>
