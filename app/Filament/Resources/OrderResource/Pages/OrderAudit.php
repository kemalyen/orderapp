<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use App\Models\Order;
use App\Models\OrderLine;
use Filament\Tables\Columns\Column;
use Filament\Resources\Pages\Page;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Actions\Action;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Livewire\Component;
use Filament\Tables\Columns\TextColumn;
use OwenIt\Auditing\Models\Audit;

class OrderAudit extends Page implements HasTable
{
    use InteractsWithTable, InteractsWithRecord;
    use InteractsWithRecord;

    protected static ?string $title = 'Order Audit';
    protected static ?string $navigationLabel = 'Order Audit';
    protected static string $resource = OrderResource::class;


    protected static string $view = 'filament.resources.order-resource.pages.order-audit';

    public function mount(int | string $record): void
    {
        $this->record = $this->resolveRecord($record);
    }

    public function table(Table $table): Table
    {
        $order_lines = $this->record->order_lines()->pluck('id')->toArray();

        return $table
            ->query(
                Audit::query()
                    ->where(
                        fn($query) => $query
                            ->where('auditable_id', $this->record->id)
                            ->where('auditable_type', Order::class)
                    )
                    ->orWhere(
                        function ($query) use ($order_lines) {
                            $query
                                ->whereIn('auditable_id', $order_lines)
                                ->where('auditable_type', OrderLine::class);
                        }
                    )
                    ->orderBy('created_at', 'desc')
            )
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('auditable_id'),
                TextColumn::make('user.name')->label('User Name')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('event')->label('Event')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('auditable_type')->label('Auditable Type')
                    ->sortable(),
                TextColumn::make('old_values')->label('Old Values')
                    ->formatStateUsing(fn(Column $column, $record, $state) => $this->record->formatAuditFieldsForPresentation($column->getName(), $record)),

                TextColumn::make('new_values')->label('New Values')
                    ->formatStateUsing(fn(Column $column, $record, $state) => $this->record->formatAuditFieldsForPresentation($column->getName(), $record)),

                TextColumn::make('created_at'),


            ])
            ->filters([
                // ...
            ])
            ->actions([
                Action::make('detail')
                    ->modal()
                    ->label('Detail')
                    ->modalContent(function ($record) {
                        if ($record->auditable_type == Order::class) {
                            $order = Order::find($record->auditable_id);
                            $order_line = null;
                        } else {
                            $order_line = OrderLine::find($record->auditable_id);
                            $order = $order_line->order;
                        } 

                        return view('filament.resources.order-resource.pages.order-audit-detail', [
                            'record' => $record,
                            'order_line' => $order_line,
                            'order' => $order,

                        ]);
                    })
                    ->modalSubmitAction(false)
                    ->modalCancelAction(fn($action) => $action->label('Close'))
                    ->icon('heroicon-o-eye')
                    ->color('primary')
            ])
            ->bulkActions([
                // ...
            ]);
    }
}
