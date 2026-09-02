<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Company>
 */
final class CompanyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->company(),
            'kvk_number' => null,
            'website' => null,
            'city' => null,
            'sbi_code' => null,
            'sbi_description' => null,
            'enriched_at' => null,
        ];
    }
}
