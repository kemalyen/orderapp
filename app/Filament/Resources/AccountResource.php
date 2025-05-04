<?php

namespace App\Filament\Resources;

use App\Enums\AccountStatus;
use App\Filament\Resources\AccountResource\Pages;
use App\Models\Account;
use App\Models\User;
use Filament\Actions\ViewAction;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

use Filament\Infolists;
use Filament\Infolists\Components\Actions\Action;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Section as InfoSection;
use Filament\Infolists\Components\TextEntry; 

class AccountResource extends Resource
{
    protected static ?string $model = Account::class;

    protected static ?int $navigationSort = 2;

    protected static ?string $navigationIcon = 'gmdi-account-balance';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                Section::make('Account Details')
                    ->description('Fill in the account details below.')
                    ->columns(1)->schema([
                        Forms\Components\TextInput::make('name')
                            ->required(),
                        Forms\Components\TextInput::make('account_number')
                            ->label('Account Number')
                            ->unique(Account::class, 'account_number', fn($record) => $record)
                            ->required(),
                        Forms\Components\Select::make('status')
                            ->options(AccountStatus::class)
                            ->getOptionLabelFromRecordUsing(fn($record) => $record->label())
                            ->label('Account Status'),
                    ])
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([

                InfoSection::make('Account Details')

                    ->schema([

                        TextEntry::make('account_number')
                            ->label('Account Number'),
                        TextEntry::make('name')
                            ->label('Account Name'),

                        TextEntry::make('created_at')
                            ->label('Created At')
                            ->dateTime(),
                        TextEntry::make('updated_at')
                            ->label('Updated At')
                            ->dateTime(),

                    ])->columns(4),

                 


            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('account_number')
                    ->searchable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->label('Created At'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    //Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListAccounts::route('/'),
            'create' => Pages\CreateAccount::route('/create'),
            'edit' => Pages\EditAccount::route('/{record}/edit'),
            'view' => Pages\ViewAccount::route('/{record}'),
        ];
    }
}
