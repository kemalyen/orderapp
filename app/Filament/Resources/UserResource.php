<?php

namespace App\Filament\Resources;

use App\Enums\AccountStatus;
use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
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

    protected static ?string $navigationIcon = 'uni-users-alt-o';

    public static function form(Form $form): Form
    {
        return $form

            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required(),
                Forms\Components\TextInput::make('email')
                    ->email()
                    ->required(),
                Section::make()->schema([

                    Select::make('roles')->relationship(
                        'roles',
                        'name'
                    )
                        ->required()
                        ->options(function () {
         
                            $roles = Role::all()->pluck('name', 'id');

                            if (auth()->user()->hasRole('Portal Admin')) {
                                return $roles;
                            }

                            $roles = $roles->filter(function ($role) {
                                return !in_array($role, ['Portal Admin', 'Portal User']);
                            });

                            return $roles->toArray();
                        }),

                    Select::make('account_id')
                        ->label('Account')
                        ->relationship('account', 'name')
                        ->required()
                        ->default(auth()->user()->account_id)
                        ->disabled(fn($record) => !auth()->user()->hasRole('Portal Admin'))

                ])->columns(2),



                Section::make()->schema([
                    TextInput::make('password')
                        ->password()
                        ->revealable(),

                    TextInput::make('password_confirmation')
                        ->password()
                        ->autocomplete('password')->same('password'),
                ])->columns(2),
            ]);
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
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('account_name')->label('Account Name'),
                Tables\Columns\TextColumn::make('roles.name'),
                Tables\Columns\TextColumn::make('created_at')
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
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('Generate API Token')
                    ->disabled(fn(User $record): bool => !$record->hasRole('Account Api User'))
                    ->hiddenLabel()
                    ->icon('heroicon-o-key')
                    ->color(fn(User $record): string => $record->hasRole('Account Api User') ? 'success' : 'secondary')
                    ->tooltip('Generate API Token')
                    ->url(fn(User $record): string => route('filament.admin.resources.users.generate-token', $record)),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
            'generate-token' => Pages\GenerateApiToken::route('/{record}/token/generate'),
        ];
    }
}
