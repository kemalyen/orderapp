<?php

namespace App\Filament\Resources\Products\Schemas;

use App\Models\Product;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Product Details')
                    ->description('Fill in the product details below.')
                    ->schema([
                        TextInput::make('name')
                            ->required()->columnSpanFull(),
                        Textarea::make('description')
                        ->rows(10)
                            ->required()
                            ->columnSpanFull(),

                        Grid::make(4)
                            ->schema([
                                TextInput::make('price')
                                    ->required()
                                    ->numeric()
                                    ->prefix('$'),

                                TextInput::make('stock')
                                    ->required()
                                    ->numeric(),

                                TextInput::make('sku')
                                    ->label('SKU')
                                    ->unique(Product::class, 'sku', fn($record) => $record)
                                    ->required(),
                                TextInput::make('barcode')
                                    ->label('Barcode')
                                    ->unique(Product::class, 'barcode', fn($record) => $record)
                                    ->required(),
                            ]),

                        FileUpload::make('image')
                            ->image()
                            ->columnSpanFull(),
                    ]),
            ])->columns(1);
    }
}
