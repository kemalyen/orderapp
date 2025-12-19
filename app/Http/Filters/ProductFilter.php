<?php

namespace App\Http\Filters;

class ProductFilter extends QueryFilter
{
    protected $sortable = [ 
        'name',
        'sku',
        'price',
        'created_at' => 'created_at'
    ];

    public function include($value)
    {
        return $this->builder->with($value);
    }

    public function status($value)
    {
        return $this->builder->where('status', $value);
    }

    public function ordered_at($value)
    {
        return $this->builder->where('ordered_at', $value);
    }

    public function sku($value)
    {
        return $this->builder->where('sku', $value);
    }
}
