<?php

namespace App\Filament\Resources\Users\Pages;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use App\Filament\Resources\Users\UserResource;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\EditRecord;
use Filament\Actions\Action;

class GenerateApiToken extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected static ?string $title = 'Generate API Token';
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-key';
    protected static ?string $breadcrumb = 'Generate API Token';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Generate API Token')
                    ->description('This will delete all existing tokens and generate a new one.')
                    ->columns(1)
                    ->schema([
                        TextInput::make('token_name')
                            ->required()
                            ->label('Token Name'),
                        TextInput::make('token')
                            ->label('Token Value')
                            ->disabled(),

                    ])

            ]);
    }

    public function generateToken(): void
    {
        $user = $this->record;

        $user->tokens()->delete();
        $token = $user->createToken($this->form->getState()['token_name'], ['order:create', 'order:read', 'order:update', 'order:delete']);
        $plainTextToken = $token->plainTextToken;

        $this->form->fill([
            'token' => $plainTextToken,
            'token_name' => $this->form->getState()['token_name'],
        ]);
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('generateToken')
                ->label('Generate Token')
                ->color('success')
                ->icon('heroicon-o-key')
                ->requiresConfirmation()
                ->modalHeading('Generate API Token')
                ->action('generateToken'),
        ];
    }

    protected function getSavedNotificationTitle(): ?string
    {
        return 'A new API is generated!';
    }

    public function getTitle(): string
    {
        return 'Generate API Token for ' . $this->record->name;
    }
}
