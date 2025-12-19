<?php

namespace App\Filament\Resources;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\EditAction;
use Filament\Actions\BulkActionGroup;
use App\Filament\Resources\AccountResource\Pages\ListAccounts;
use App\Filament\Resources\AccountResource\Pages\CreateAccount;
use App\Filament\Resources\AccountResource\Pages\EditAccount;
use App\Filament\Resources\AccountResource\Pages\ViewAccount;
use App\Enums\AccountStatus;
use App\Filament\Resources\AccountResource\Pages;
use App\Models\Account;
use App\Models\User;
use Filament\Actions\ViewAction;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

use Filament\Infolists;
use Filament\Infolists\Components\Actions\Action;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry; 

class AccountResource extends Resource
{
    protected static ?string $model = Account::class;

    protected static ?int $navigationSort = 2;

    protected static string | \BackedEnum | null $navigationIcon = 'gmdi-account-balance';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([

                Section::make('Account Details')
                    ->description('Fill in the account details below.')
                    ->columns(1)->schema([
                        TextInput::make('name')
                            ->required(),
                        TextInput::make('account_number')
                            ->label('Account Number')
                            ->unique(Account::class, 'account_number', fn($record) => $record)
                            ->required(),
                        Select::make('status')
                            ->options(AccountStatus::class)
                            ->getOptionLabelFromRecordUsing(fn($record) => $record->label())
                            ->label('Account Status'),
                    ])
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([

                Section::make('Account Details')

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
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('account_number')
                    ->searchable(),
                TextColumn::make('status')
                    ->badge(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->label('Created At'),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
                ViewAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
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
            'index' => ListAccounts::route('/'),
            'create' => CreateAccount::route('/create'),
            'edit' => EditAccount::route('/{record}/edit'),
            'view' => ViewAccount::route('/{record}'),
        ];
    }
}
