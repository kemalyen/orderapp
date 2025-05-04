<?php

namespace App\Filament\Resources\OrderResource\Widgets;

use App\Enums\OrderStatus;
use App\Models\Order;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Pending orders', Order::where('status', OrderStatus::PENDING->value)->count())
            ->description('The orders that are pending')
            ->color('danger'),
            Stat::make('Approved orders', Order::where('status', OrderStatus::APPROVED->value)->count())
            ->description('The orders that are approved but didn\'t processed yet')
            ->color('warning'),
            Stat::make('Processing orders', Order::where('status', OrderStatus::PROCESSING->value)->count())
            ->description('The orders that are being processed')
            ->color('info'),
            Stat::make('Completed orders', 
                        Order::where('status', OrderStatus::COMPLETED->value)
                        ->whereDate('created_at', '>=', Carbon::now()->subDays(30))
                        ->count())
                        ->description('The orders completed in the last 7 days')
                        ->color('success'),

   
        ];
    }
    protected function getHeading(): ?string
    {
        return 'Orders Overview';
    }

    public static function canView(): bool
    { 
        return auth()->user()->hasRole('Portal Admin') || auth()->user()->hasRole('Portal User')
            ? true
            : false;
    }
 
}
        
