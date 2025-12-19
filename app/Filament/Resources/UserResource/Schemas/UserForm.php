<?php

namespace App\Filament\Resources\UserResource\Schemas;

use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Spatie\Permission\Models\Role;


class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema


            ->components([
                Section::make()->schema([
                    TextInput::make('name')
                        ->required(),
                    TextInput::make('email')
                        ->email()
                        ->required(),
                ])->columns(2),


                Section::make()->schema([
                    Section::make()->schema([
                        TextInput::make('password')
                            ->password()
                            ->revealable(),

                        TextInput::make('password_confirmation')
                            ->password()
                            ->autocomplete('password')->same('password'),
                    ])->columns(2),
                ])->columns(1),

                Section::make()->schema([

                    CheckboxList::make('roles')
                        ->relationship('roles', 'name')
                        ->columns(1)
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


            ])->columns(1);
    }
}
