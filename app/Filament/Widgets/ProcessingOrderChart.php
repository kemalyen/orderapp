<?php

namespace App\Filament\Widgets;

use App\Enums\OrderStatus;
use App\Models\Order;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;
use Illuminate\Support\Carbon;

class ProcessingOrderChart extends ChartWidget
{
    protected static ?string $heading = 'Processing Orders';
    protected static ?int $sort = 3;
    protected function getData(): array
    {
        $data = Trend::query(Order::where('status', OrderStatus::PROCESSING->value))
            ->between(
                start: Carbon::now()->subDays(30),
                end: Carbon::now(),
            )
            ->perDay()
            ->count();

        return [
            'datasets' => [
                [
                    'label' => 'Daily Processing Orders',
                    'data' => $data->map(fn (TrendValue $value) => $value->aggregate),
                ],
            ],
            'labels' => $data->map(fn (TrendValue $value) => $value->date),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    public static function canView(): bool
    { 
        return auth()->user()->hasRole('Portal Admin') || auth()->user()->hasRole('Portal User')
            ? true
            : false;
    }
}
