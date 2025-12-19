<?php

namespace App\Filament\Resources\Orders\Pages;

use Filament\Actions\CreateAction;
use App\Filament\Resources\Orders\Widgets\StatsOverview;
use App\Filament\Resources\Orders\OrderResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListOrders extends ListRecords
{
    protected static string $resource = OrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [

            StatsOverview::class,
        ];
    }
}
