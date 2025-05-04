<?php

namespace App\Http\Filters;

class OrderFilter extends QueryFilter
{

    protected $sortable = [
        'account_name',
        'order_number',
        'status',
        'ordered_at' => 'ordered_at'
    ];

    public function include($value)
    {
        return $this->builder->with($value);
    }

    public function status($value)
    {
        return $this->builder->where('status', $value);
    }

    public function account($value)
    {
        $likeStr = str_replace('*', '%', $value);
        return $this->builder->whereHas(
            'account',
            function ($query) use ($likeStr) {
                $query->where('name', 'like', $likeStr)
                    ->orWhere('account_number', $likeStr);
            }
        );
    }

    public function ordered_at($value)
    {
        return $this->builder->where('ordered_at', $value);
    }

    public function order_number($value)
    {
        return $this->builder->where('order_number', $value);
    }
}
