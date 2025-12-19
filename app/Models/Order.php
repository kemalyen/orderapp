<?php

namespace App\Models;

use Database\Factories\OrderFactory;
use App\Enums\OrderStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Http\Filters\QueryFilter;
use Illuminate\Database\Eloquent\Builder;
use OwenIt\Auditing\Contracts\Audit;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Support\Arr;
use Illuminate\Support\HtmlString;

class Order extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    /** @use HasFactory<OrderFactory> */
    use HasFactory;

    protected $fillable = [
        'account_id',
        'order_number',
        'status',
        'shipping_address',
        'ordered_at',
        'order_amount',
    ];
    protected $casts = [
        'ordered_at' => 'datetime',
        'status' => OrderStatus::class,
    ];
    protected $attributes = [
        'status' => 'Pending',
        'account_id' => 1,
    ];

    protected $hidden = [
        'account_id',
    ];
 
    public function scopeFilter(Builder $builder, QueryFilter $filters)
    {
        return $filters->apply($builder);
    }

    public function order_lines()
    {
        return $this->hasMany(OrderLine::class, 'order_number', 'order_number');
    }

    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    public function getOrderAmountAttribute()
    {
        return $this->order_lines->sum('line_total');
    }

    public function getTotalQuantityAttribute()
    {
        return $this->order_lines->sum('quantity');
    }
    public function getTotalItemsAttribute()
    {
        return $this->order_lines->count();
    }

    public function getAccountNameAttribute()
    {
        return $this->account?->name;
    }

    public function getRouteKey()
    {
        return $this->order_number;
    }

    public function getRouteKeyName()
    {
        return 'order_number';
    }

    public function formatAuditFieldsForPresentation($field, Audit $record)
    {
        $fields = Arr::wrap($record->{$field});
 

        $formattedResult = '<ul>';
 
        foreach ($fields as $key => $value) {
            $formattedResult .= '<li>';
            $formattedResult .= match ($key) {
                'order_number' => '<strong>Order Number</strong>: '.(string) str($record->{$field}['order_number']).'<br />',
                
                'status' => '<strong>Status</strong>: '.$record->{$field}['status'].'<br />',
                'account_id' => '<strong>Account</strong>: '.(string) str($record->{$field}['account_id'])->title().'<br />',
                'ordered_at' => '<strong>Ordered at</strong>: '.(string) str($record->{$field}['ordered_at'])->title().'<br />',

                'sku' => '<strong>SKU</strong>: '.(string) str($record->{$field}['sku'])->title().'<br />',
                'quantity' => '<strong>Quantity</strong>: '.(string) str($record->{$field}['quantity'])->title().'<br />',
                'price' => '<strong>Price</strong>: '.(string) str($record->{$field}['price'])->title().'<br />',
                default => '',
            };
            $formattedResult .= '</li>';
        }
 
        $formattedResult .= '</ul>';
 
        return new HtmlString($formattedResult);
    }
}
