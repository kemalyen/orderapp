<div>
    <table class="table-fixed   w-full text-sm text-left text-gray-500 dark:text-gray-400 gap-4">
       
        <tr>
            <td class="bg-body-info">Order ID</td>
            <td class="pl-4">{{ $order->order_number}}</td>

            <td class="bg-body-info">Order Status</td>
            <td class="pl-4">{{ $order->status}}</td>
        </tr>
       
        @if($order_line)
 
        <tr>
            <td class="bg-body-info">Product SKU</td>
            <td class="pl-4">{{ $order_line->product_sku}}</td>


            <td class="bg-body-info">Product Name</td>
            <td class="pl-4">{{ $order_line->product_title}}</td>

        </tr>

        <tr>
            <td class="bg-body-info">Created At</td>
            <td class="pl-4">{{ $order_line->created_at}}</td>

            <td class="bg-body-info">Updated At</td>
            <td class="pl-4">{{ $order_line->updated_at}}</td>
        </tr>
        @endif
    </table>
</div>