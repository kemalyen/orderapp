<?php

namespace Database\Factories;

use App\Enums\AccountStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Account>
 */
class AccountFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->company(),
            'status' => fake()->randomElement(AccountStatus::cases())->value,
            'account_number' => fake()->randomNumber(5, true)
        ];
    }
}