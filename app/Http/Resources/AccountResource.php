<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AccountResource extends JsonResource
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
                'account_name' => $this->account_name,
                'account_id' => $this->account_id,
                'account_number' => $this->account_number,
                'created_at' => $this->created_at,
            ],
            
        ];
    }
}
