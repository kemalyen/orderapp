<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'type' => 'orders',
            'attributes' => [
                'order_number' => $this->order_number,
                'status' => $this->status,
                'shipping_address' => $this->shipping_address,  
                'ordered_at' => $this->ordered_at,
                'created_at' => $this->created_at,
                'updated_at' => $this->updated_at,
            ],

            'relationships' => [
                'order_lines' => $this->whenLoaded('order_lines', function () {
                    return OrderLineResource::collection($this->order_lines);
                }),
                'account' => new AccountResource($this->account)
            ],
            'links' => [
                'self' => route('orders.show', $this->order_number),
            ],
            'meta' => [
                'order_amount' => $this->order_amount,
                'total_quantity' => $this->total_quantity,
            ],
        ];
    }
}
