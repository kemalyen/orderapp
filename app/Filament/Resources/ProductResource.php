<?php

namespace App\Filament\Resources;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\FileUpload;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\EditAction;
use Filament\Actions\BulkActionGroup;
use App\Filament\Resources\ProductResource\Pages\ListProducts;
use App\Filament\Resources\ProductResource\Pages\CreateProduct;
use App\Filament\Resources\ProductResource\Pages\EditProduct;
use App\Filament\Resources\ProductResource\Pages;
use App\Filament\Resources\ProductResource\RelationManagers;
use App\Models\Product;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static string | \BackedEnum | null $navigationIcon = 'far-list-alt';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Product Details')
                    ->description('Fill in the product details below.')
                    ->schema([
                        TextInput::make('name')
                            ->required()->columnSpanFull(),
                        Textarea::make('description')
                            ->required()
                            ->columnSpanFull(),

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


                        FileUpload::make('image')
                            ->image()
                            ->columnSpanFull(),
                    ]), 
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('price')
                    ->money(),

                TextColumn::make('stock')
                    ->numeric(),
                TextColumn::make('sku')
                    ->label('SKU')
                    ->searchable(),
                TextColumn::make('barcode'),
                TextColumn::make('created_at')
                    ->dateTime()

            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    // Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListProducts::route('/'),
            'create' => CreateProduct::route('/create'),
            'edit' => EditProduct::route('/{record}/edit'),
        ];
    }
}
