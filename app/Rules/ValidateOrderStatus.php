<?php

namespace App\Rules;

use Illuminate\Translation\PotentiallyTranslatedString;
use App\Enums\OrderStatus;
use App\Models\Order;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ValidateOrderStatus implements ValidationRule
{


    public function __construct(
        protected OrderStatus $currentStatus,
    ) {
        //
    }
    /**
     * Run the validation rule.
     *
     * @param Closure(string, ?string=):PotentiallyTranslatedString $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!OrderStatus::canTransition($this->getCurrentStatus(), $this->getToStatus($value))) {
            $fail("The order cannot be transitioned from {$this->currentStatusText()} to {$this->getValue($value)}.");
        }
    }

    public function currentStatusText(): string
    {
        return $this->currentStatus->value;
    }

    public function getCurrentStatus(): OrderStatus
    {
        return $this->currentStatus;
    }

    private function getToStatus($value): OrderStatus
    {
        if (is_string($value)) {
            $value = OrderStatus::from($value);
        }
        if ($value instanceof OrderStatus) {
            return $value;
        }
        return OrderStatus::from($value);
    }


    private function getValue($value): string
    {
        if ($value instanceof OrderStatus) {
            return $value->value;
        }
        return OrderStatus::from($value)->value;
    }
}
