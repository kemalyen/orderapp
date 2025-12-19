<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    protected function handleRecordCreation(array $data): Model
    {

        if (auth()->user()->hasRole('Account Admin')) {
            $data['account_id'] = auth()->user()->account_id;
        }

        // Create the user with the provided data
        // and assign the 'Account Admin' role if the user is a Portal Admin
        if (!auth()->user()->hasRole('Portal Admin')) {
            $data['roles'] = ['Account User'];
        }
        // Create the user
        $user = UserResource::getModel()::create($data);

        if (isset($data['roles'])) {
            $user->syncRoles($data['roles']);
        }

        return $user;
    }
}