<?php

namespace App\Filament\Resources\Orders;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\DateTimePicker;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use App\Models\Product;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use App\Filament\Resources\Orders\Pages\ListOrders;
use App\Filament\Resources\Orders\Pages\CreateOrder;
use App\Filament\Resources\Orders\Pages\EditOrder;
use App\Filament\Resources\Orders\Pages\ViewOrder;
use App\Filament\Resources\Orders\Pages\OrderAudit;
use App\Enums\OrderStatus;
use App\Filament\Resources\OrderResource\Pages;
use App\Filament\Resources\OrderResource\RelationManagers;
use App\Models\Order;
use App\Rules\ValidateOrderStatus;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Model;
use Filament\Infolists;
use Filament\Infolists\Components\Fieldset;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Support\Facades\Log;
use Closure;
use Tapp\FilamentAuditing\RelationManagers\AuditsRelationManager;


class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Order Details')
                    ->description('Fill in the order details below.')
                    ->schema([
                        TextInput::make('order_number')
                            ->unique(Order::class, 'order_number', fn($record) => $record)
                            ->required(),
                        Select::make('account_id')
                            ->label('Account')
                            ->relationship('account', 'name')
                            ->required(),
                        Select::make('status')
                            ->options(OrderStatus::class)
                            ->getOptionLabelFromRecordUsing(fn($record) => $record->label())
                            ->label('Order Status')
                            ->hiddenOn('create')
                            ->default(OrderStatus::PENDING)
                            ->rules([
                                fn($record) => new ValidateOrderStatus(($record ? $record->status : OrderStatus::PENDING)),
                            ])
                            ->required(),

                        DateTimePicker::make('ordered_at')
                            ->label('Ordered At')
                            ->default(now())

                    ])->columns(4),

                Section::make('Ordered Items')
                    ->schema([
                        Repeater::make('order_lines')

                            ->hiddenLabel()
                            ->schema([
                                Select::make('sku')
                                    ->label('Product SKU')
                                    ->relationship('product', 'sku')
                                    ->searchable(['sku', 'name'])
                                    ->getOptionLabelFromRecordUsing(fn(Model $record) => "{$record->sku} {$record->name}")
                                    ->live()
                                    ->afterStateUpdated(function (Get $get, Set $set, ?string $old, ?string $state) {
                                        $product = Product::where('sku', $state)->first();
                                        if ($product) {
                                            $set('product_title', $product->name);
                                            $set('price', $product->price);
                                        }
                                    })->disabledOn('edit')
                                    ->required(),
                                TextInput::make('quantity')
                                    ->required()
                                    ->numeric()
                                    ->default(1)
                                    ->minValue(1)
                                    ->rules([
                                        fn(Get $get): Closure => function (string $attribute, $value, Closure $fail) use ($get) {
                                            $sku = $get('sku');
                                            $product = Product::where('sku', $sku)->first();
                                            if ($value > $product->stock) {
                                                $fail("The quantity exceeds the available stock of {$product->stock}.");
                                            }
                                        },
                                    ])
                                    ->label('Quantity'),

                                TextInput::make('price')
                                    ->required()
                                    ->rules([
                                        fn(Get $get): Closure => function (string $attribute, $value, Closure $fail) use ($get) {
                                            $sku = $get('sku');
                                            $product = Product::where('sku', $sku)->first();
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
            ])->columns(1);
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
                SelectFilter::make('status')
                    ->label('Order Status')
                    ->options(OrderStatus::class),


                SelectFilter::make('account_id')
                    ->label('Account')
                    ->relationship('account', 'id')
                    ->getOptionLabelFromRecordUsing(fn($record) => $record->name)
                    ->hidden(fn(): bool => !auth()->user()->hasRole('Portal Admin') || !auth()->user()->hasRole('Portal User'))
            ])
            ->recordActions([
                EditAction::make(),
                ViewAction::make(),
                Action::make('order-audit')
                    ->icon('heroicon-o-eye')
                    ->tooltip('View Audit Logs')
                    ->color('secondary')
                    ->label('Audit Logs')
                    ->url(fn(Order $record): string => route('filament.admin.resources.orders.order-audit', $record))
                    ->hidden(fn(): bool => !auth()->user()->hasRole('Portal Admin'))

            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }



    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([

                Section::make('Order Details')

                    ->schema([

                        TextEntry::make('order_number'),
                        TextEntry::make('account_name')
                            ->label('Account Name'),
                        TextEntry::make('status')->badge(),
                        TextEntry::make('order_amount')
                            ->label('Total Amount'),
                        TextEntry::make('ordered_at')
                            ->label('Ordered At')
                            ->dateTime(),
                        TextEntry::make('created_at')
                            ->label('Created At')
                            ->dateTime(),
                        TextEntry::make('updated_at')
                            ->label('Updated At')
                            ->dateTime(),

                    ])->columns(4)->columnSpanFull(),

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
            'index' => ListOrders::route('/'),
            'create' => CreateOrder::route('/create'),
            'edit' => EditOrder::route('/{record}/edit'),
            'view' => ViewOrder::route('/{record}'),
            'order-audit' => OrderAudit::route('/{record}/audit-logs'),
        ];
    }
}
