<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Filament\Resources\ProductResource\RelationManagers;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'far-list-alt';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Product Details')
                    ->description('Fill in the product details below.')
                    ->columns(2)->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()->columnSpanFull(),
                        Forms\Components\Textarea::make('description')
                            ->required()
                            ->columnSpanFull(),

                        Forms\Components\TextInput::make('price')
                            ->required()
                            ->numeric()
                            ->prefix('$'),

                        Forms\Components\TextInput::make('stock')
                            ->required()
                            ->numeric(),


                        Forms\Components\TextInput::make('sku')
                            ->label('SKU')
                            ->unique(Product::class, 'sku', fn($record) => $record)
                            ->required(),
                        Forms\Components\TextInput::make('barcode')
                            ->label('Barcode')
                            ->unique(Product::class, 'barcode', fn($record) => $record)
                            ->required(),


                        Forms\Components\FileUpload::make('image')
                            ->image()
                            ->columnSpanFull(),
                    ]),




            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('price')
                    ->money() ,
                 
                Tables\Columns\TextColumn::make('stock')
                    ->numeric()
                    ,
                Tables\Columns\TextColumn::make('sku')
                    ->label('SKU')
                    ->searchable(),
                Tables\Columns\TextColumn::make('barcode')
                   ,
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                     
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
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
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
