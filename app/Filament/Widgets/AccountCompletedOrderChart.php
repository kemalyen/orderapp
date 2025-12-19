<?php

namespace App\Filament\Widgets;

use App\Enums\OrderStatus;
use App\Models\Order;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;
use Illuminate\Support\Carbon;

class AccountCompletedOrderChart extends ChartWidget
{
    protected ?string $heading = 'Processed Orders';
    protected static ?int $sort = 2;

    protected function getData(): array
    {
        $user = auth()->user();
        $data = Trend::query(Order::where('status', OrderStatus::COMPLETED->value)->where('account_id', $user->account_id))
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
                    'data' => $data->map(fn(TrendValue $value) => $value->aggregate),
                ],
            ],
            'labels' => $data->map(fn(TrendValue $value) => $value->date),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    public static function canView(): bool
    {
        return auth()->user()->hasRole('Account Admin') || auth()->user()->hasRole('Account User')
            ? true
            : false;
    }
}
