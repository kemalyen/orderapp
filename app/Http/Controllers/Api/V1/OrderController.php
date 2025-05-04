<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Filters\OrderFilter;
use App\Http\Requests\StoreOrderRequest;
use App\Http\Requests\UpdateOrderRequest;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Models\OrderLine;
use App\Models\User;

class OrderController extends ApiController
{

    public function __construct()
    {
        $this->authorizeResource(Order::class);
    }

    /**
     * List all orders
     * 
     * @group Order API Resource
     * @queryParam sort by order name
     * @queryParam filter[title] Filter by name. Wildcards are supported. Example: *fix*
     */
    public function index(OrderFilter $orderFilter)
    {
        return OrderResource::collection(
            Order::filter($orderFilter)->orderBy('created_at', 'DESC')->paginate()
        );
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreOrderRequest $request)
    {
        $account = auth()->user()->account;
        $order = Order::create([
            'account_id' => $account->id,
            'order_number' => $request->order_number,
        ]);

        $items = collect($request->order_lines)->map(function ($line) {
            return new OrderLine([
                'sku' => $line['sku'],
                'quantity' => $line['quantity'],
                'price' => $line['price'],
                'line_total' => $line['quantity'] * $line['price'],
            ]);
        });

        $order->order_lines()->saveMany($items);
        // Return a response or redirect
        return new OrderResource($order->load('order_lines'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Order $order)
    {
        if ($this->include('order_lines')) {
            return new OrderResource($order->load('order_lines'));
        }

        return new OrderResource($order);
    }

}
