<?php

namespace App\Enums;


use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum OrderStatus: string implements HasLabel, HasColor
{
    case PROCESSING = 'Processing';
    case PENDING = 'Pending';
    case CANCELLED = 'Cancelled';
    case COMPLETED = 'Completed';
    case APPROVED = 'Approved';
    case REJECTED = 'Rejected';



    public function orderStatus(string $name): string
    {
        return match ($name) {
            'Processing' => self::PROCESSING->value,
            'Pending' =>  self::PENDING->value,
            'Cancelled' => self::CANCELLED->value,
            'Completed' => self::COMPLETED->value,
            'Approved' => self::APPROVED->value,
            'Rejected' => self::REJECTED->value,
            default => self::PENDING->value,
        };
    }

    public function getLabel(): ?string
    {
        return $this->name;
    }

    public function getColor(): ?string
    {
        return match ($this->value) {
            'Completed' => 'success',
            'Cancelled' => 'danger',
            'Pending' => 'info',
            'Processing' => 'warning',
            'Approved' => 'success',
            'Rejected' => 'danger',
        };
    }

    public static function canTransition(self $fromStatus, self $toStatus): bool
    {
        return match ([$fromStatus, $toStatus]) {
            [self::PENDING, self::PENDING] => true,
            [self::PENDING, self::CANCELLED] => true,
            [self::PROCESSING, self::CANCELLED] => true,
            [self::PROCESSING, self::PENDING] => true,
            [self::PENDING, self::APPROVED] => true,
            [self::APPROVED, self::PROCESSING] => true,
            [self::APPROVED, self::PENDING] => true,
            default => false,
        };
    }
}
