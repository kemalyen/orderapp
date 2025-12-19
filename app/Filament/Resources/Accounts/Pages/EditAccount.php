<?php

namespace App\Filament\Resources\Accounts\Pages;

use Filament\Actions\DeleteAction;
use App\Filament\Resources\Accounts\AccountResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAccount extends EditRecord
{
    protected static string $resource = AccountResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
