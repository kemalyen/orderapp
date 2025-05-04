<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderLineResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
return [
            'type' => 'order_lines',
            'attributes' => [
                'sku' => $this->sku,
                'quantity' => $this->quantity,
                'price' => $this->price,
                'line_total' => $this->line_total,
                'created_at' => $this->created_at,
                'updated_at' => $this->updated_at,
            ],
            
        ];
    }
}
