<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Filament\Actions\BulkActionGroup;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;

class LatestOrders extends TableWidget
{
    protected int | string | array $columnSpan = 'full';
    
    protected static ?int $sort = 12;

    public function table(Table $table): Table
    {
        if (auth()->user()->hasRole('Account Admin') || auth()->user()->hasRole('Account User')){
            $where = Order::query()->where('account_id', auth()->user()->account_id);
        }
 
        return $table
            ->query(fn (): Builder => $where ?? Order::query())
            ->columns([
                TextColumn::make('account_name')
                    ->label('Account Name')
                    ->hidden(fn(): bool => !auth()->user()->hasRole('Portal Admin') || !auth()->user()->hasRole('Portal User'))
                    ->sortable(),
                TextColumn::make('order_number')
                    ->searchable(),
                TextColumn::make('order_amount')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('status')
                    ->badge(),
                TextColumn::make('ordered_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                //
            ])
            ->recordActions([
                //
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    //
                ]),
            ]);
    }
}
