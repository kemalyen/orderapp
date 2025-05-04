<?php

namespace App\Filament\Resources;

use App\Enums\OrderStatus;
use App\Filament\Resources\OrderResource\Pages;
use App\Filament\Resources\OrderResource\RelationManagers;
use App\Models\Order;
use App\Rules\ValidateOrderStatus;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Model;
use Filament\Infolists;
use Filament\Infolists\Components\Fieldset;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\Section as InfoSection;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Support\Facades\Log;
use Closure;
use Tapp\FilamentAuditing\RelationManagers\AuditsRelationManager;


class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Order Details')
                    ->description('Fill in the order details below.')
                    ->columns(2)->schema([
                        Forms\Components\TextInput::make('order_number')
                            ->unique(Order::class, 'order_number', fn($record) => $record)
                            ->required(),
                        Select::make('account_id')
                            ->label('Account')
                            ->relationship('account', 'name')
                            ->required(),
                        Forms\Components\Select::make('status')
                            ->options(OrderStatus::class)
                            ->getOptionLabelFromRecordUsing(fn($record) => $record->label())
                            ->label('Order Status')
                            ->hiddenOn('create')
                            ->default(OrderStatus::PENDING)
                            ->rules([
                                fn($record) => new ValidateOrderStatus(($record ? $record->status : OrderStatus::PENDING)),
                            ])
                            ->required(),

                        Forms\Components\DateTimePicker::make('ordered_at')
                            ->label('Ordered At')
                            ->default(now())

                    ])->columns(4),

                Section::make('Ordered Items')
                    ->columns(2)->schema([
                        Repeater::make('order_lines')

                            ->hiddenLabel()
                            ->schema([
                                Forms\Components\Select::make('sku')
                                    ->label('Product SKU')
                                    ->relationship('product', 'sku')
                                    ->searchable(['sku', 'name'])
                                    ->getOptionLabelFromRecordUsing(fn(Model $record) => "{$record->sku} {$record->name}")
                                    ->live()
                                    ->afterStateUpdated(function (Get $get, Set $set, ?string $old, ?string $state) {
                                        $product = \App\Models\Product::where('sku', $state)->first();
                                        if ($product) {
                                            $set('product_title', $product->name);
                                            $set('price', $product->price);
                                        }
                                    })->disabledOn('edit')
                                    ->required(),
                                Forms\Components\TextInput::make('quantity')
                                    ->required()
                                    ->numeric()
                                    ->default(1)
                                    ->minValue(1)
                                    ->rules([
                                        fn(Get $get): Closure => function (string $attribute, $value, Closure $fail) use ($get) {
                                            $sku = $get('sku');
                                            $product = \App\Models\Product::where('sku', $sku)->first();
                                            if ($value > $product->stock) {
                                                $fail("The quantity exceeds the available stock of {$product->stock}.");
                                            }
                                        },
                                    ])
                                    ->label('Quantity'),

                                Forms\Components\TextInput::make('price')
                                    ->required()
                                    ->rules([
                                        fn(Get $get): Closure => function (string $attribute, $value, Closure $fail) use ($get) {
                                            $sku = $get('sku');
                                            $product = \App\Models\Product::where('sku', $sku)->first();
                                            if ($value < $product->price) {
                                                $fail("The price is less than the product price of {$product->price}.");
                                            }
                                        },
                                    ])
                                    ->label('Price'),

                            ])
                            ->columns(3)
                            ->relationship('order_lines')
                    ])->columns(1)
            ]);
    }

    public static function table(Table $table): Table
    {
        $user = auth()->user();
        return $table
            ->query(Order::query()->with('account')->when($user->hasRole('Account Admin'), function (Builder $query) use ($user) {
                $query->where('account_id', $user->account_id);
            }))
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('account_name')
                    ->label('Account Name')
                    ->hidden(fn(): bool => !auth()->user()->hasRole('Portal Admin') || !auth()->user()->hasRole('Portal User'))
                    ->sortable(),
                Tables\Columns\TextColumn::make('order_number')
                    ->searchable(),
                Tables\Columns\TextColumn::make('order_amount')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge(),
                Tables\Columns\TextColumn::make('ordered_at')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Order Status')
                    ->options(OrderStatus::class),


                SelectFilter::make('account_id')
                    ->label('Account')
                    ->relationship('account', 'id')
                    ->getOptionLabelFromRecordUsing(fn($record) => $record->name)
                    ->hidden(fn(): bool => !auth()->user()->hasRole('Portal Admin') || !auth()->user()->hasRole('Portal User'))
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make(),
                Tables\Actions\Action::make('order-audit')
                    ->icon('heroicon-o-eye')
                    ->tooltip('View Audit Logs')
                    ->color('secondary')
                    ->label('Audit Logs')
                    ->url(fn(Order $record): string => route('filament.admin.resources.orders.order-audit', $record))
                    ->hidden(fn(): bool => !auth()->user()->hasRole('Portal Admin'))

            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }



    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([

                InfoSection::make('Order Details')

                    ->schema([

                        Infolists\Components\TextEntry::make('order_number'),
                        Infolists\Components\TextEntry::make('account_name')
                            ->label('Account Name'),
                        Infolists\Components\TextEntry::make('status')->badge(),
                        Infolists\Components\TextEntry::make('order_amount')
                            ->label('Total Amount'),
                        Infolists\Components\TextEntry::make('ordered_at')
                            ->label('Ordered At')
                            ->dateTime(),
                        Infolists\Components\TextEntry::make('created_at')
                            ->label('Created At')
                            ->dateTime(),
                        Infolists\Components\TextEntry::make('updated_at')
                            ->label('Updated At')
                            ->dateTime(),

                    ])->columns(4),

                RepeatableEntry::make('order_lines')->label('Ordered Items')
                    ->schema([
                        TextEntry::make('product.sku')
                            ->label('Product SKU'),
                        TextEntry::make('product.name')
                            ->label('Product Name'),
                        TextEntry::make('quantity'),
                        TextEntry::make('price'),
                        TextEntry::make('line_total'),

                    ])
                    ->columns(5)

                    ->columnSpanFull(),


            ]);
    }


    public static function getRelations(): array
    {
        return [
            //AuditsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
            'view' => Pages\ViewOrder::route('/{record}'),
            'order-audit' => Pages\OrderAudit::route('/{record}/audit-logs'),
        ];
    }
}
