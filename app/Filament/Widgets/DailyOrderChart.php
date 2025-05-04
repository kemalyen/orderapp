<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;
use Illuminate\Support\Carbon;

class DailyOrderChart extends ChartWidget
{
    protected static ?string $heading = 'Daily Orders received in Last 30 days';
    protected static ?int $sort = 1;
     

    protected function getData(): array
    {
        $data = Trend::model(Order::class)
            ->between(
                start: Carbon::now()->subDays(30),
                end: Carbon::now(),
            )
            ->perDay()
            ->count();

        return [
            'datasets' => [
                [
                    'label' => 'Daily Orders',
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
