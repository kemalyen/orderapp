<?php

namespace App\Enums;


use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum AccountStatus: string implements HasLabel, HasColor
{
    case ACTIVE  = 'Active';
    case PENDING = 'Pending';
    case DISABLED = 'Disabled';

    public function accountStatus(string $name): string
    {
        return match ($name) {
            'Active' => self::ACTIVE->value,
            'Pending' => self::PENDING->value,
            'Disabled' => self::DISABLED->value,
        };
    }

    public function getLabel(): ?string
    {
        return $this->name;
    }

    public function getColor(): ?string
    {
        return match ($this->value) {
            'Active' => 'success',
            'Disabled' => 'danger',
            'Pending' => 'warning',
        };
    }
}
