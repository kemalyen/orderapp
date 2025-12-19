<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'type' => 'products',
            'attributes' => [
                'name' => $this->name,
                'sku' => $this->sku,
                'description' => $this->description,
                'price' => $this->price,
                'status' => $this->status,
                'created_at' => $this->created_at,
            ],

 
            'links' => [
                'self' => route('products.show', $this->sku),
            ] 
        ];
    }
}
