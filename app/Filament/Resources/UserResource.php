<?php

namespace App\Filament\Resources;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\EditAction;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use App\Filament\Resources\UserResource\Pages\ListUsers;
use App\Filament\Resources\UserResource\Pages\CreateUser;
use App\Filament\Resources\UserResource\Pages\EditUser;
use App\Filament\Resources\UserResource\Pages\GenerateApiToken;
use App\Enums\AccountStatus;
use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Filament\Resources\UserResource\Schemas\UserForm;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Spatie\Permission\Models\Role;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static string | \BackedEnum | null $navigationIcon = 'uni-users-alt-o';

    public static function form(Schema $schema): Schema
    {
        return UserForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        $user = auth()->user();

        $query = User::with(['account', 'roles'])
            ->when($user->hasRole('Account Admin'), function (Builder $query) use ($user) {
                $query->where('account_id', $user->account_id);
            });


        return $table
            ->query($query)
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('email')
                    ->searchable(),
                TextColumn::make('account_name')->label('Account Name'),
                TextColumn::make('roles.name'),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
            ])
            ->filters([
                SelectFilter::make('account_id')
                    ->label('Account')
                    ->relationship('account', 'id')
                    ->getOptionLabelFromRecordUsing(fn($record) => $record->name),

                SelectFilter::make('roles')
                    ->label(__('User Role'))
                    ->relationship('roles', 'name')
                    ->getOptionLabelFromRecordUsing(fn($record) => $record->name)
            ])
            ->recordActions([
                EditAction::make(),
                Action::make('Generate API Token')
                    ->disabled(fn(User $record): bool => !$record->hasRole('Account Api User'))
                    ->hiddenLabel()
                    ->icon('heroicon-o-key')
                    ->color(fn(User $record): string => $record->hasRole('Account Api User') ? 'success' : 'secondary')
                    ->tooltip('Generate API Token')
                    ->url(fn(User $record): string => route('filament.admin.resources.users.generate-token', $record)),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
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
            'index' => ListUsers::route('/'),
            'create' => CreateUser::route('/create'),
            'edit' => EditUser::route('/{record}/edit'),
            'generate-token' => GenerateApiToken::route('/{record}/token/generate'),
        ];
    }
}
