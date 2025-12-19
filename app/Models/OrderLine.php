<?php

namespace App\Models;

use Database\Factories\OrderLineFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Support\Arr;
use Illuminate\Support\HtmlString;
use OwenIt\Auditing\Contracts\Audit;

class OrderLine extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    /** @use HasFactory<OrderLineFactory> */
    use HasFactory;

    protected $fillable = [
        'order_number',
        'sku',
        'product_title',
        'quantity',
        'price',
        'line_total'
    ];

    protected $casts = [
        'quantity' => 'integer',
        'price' => 'decimal:2',
    ];

    protected $attributes = [
        'quantity' => 1,
    ];
 

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_number', 'order_number');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'sku', 'sku');
    }
    
    public function getProductTitleAttribute()
    {
        return $this->product->name ?? null;
    }

    public function getProductPriceAttribute()
    {
        return $this->product->price ?? null;
    }

    public function getProductStockAttribute()
    {
        return $this->product->stock ?? null;
    }

    public function getProductSkuAttribute()
    {
        return $this->product->sku ?? null;
    }

    protected static function booted()
    {
 
        static::saving(function ($orderLine) {
            $line_total = $orderLine->quantity * $orderLine->price;
            $orderLine->line_total = $line_total;
        });

    }


    public function formatAuditFieldsForPresentation($field, Audit $record)
    {
        $fields = Arr::wrap($record->{$field});
 
        $formattedResult = '<ul>';
 
        foreach ($fields as $key => $value) {
            $formattedResult .= '<li>';
            $formattedResult .= match ($key) {
                'quantity' => '<strong>Quantity</strong>: '.(string) str($record->{$field}['quantity'])->title().'<br />',
                'sku' => '<strong>SKU</strong>: '.(string) str($record->{$field}['sku'])->title().'<br />',
                default => ' - ',
            };
            $formattedResult .= '</li>';
        }
 
        $formattedResult .= '</ul>';
 
        return new HtmlString($formattedResult);
    }
}
