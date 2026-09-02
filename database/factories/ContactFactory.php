<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Company;
use App\Models\Contact;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Contact>
 */
final class ContactFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'company_id' => Company::factory(),
            'name' => $this->faker->name(),
            'role' => null,
            'email' => null,
            'phone' => null,
        ];
    }

    /** @param Company|Factory<Company> $company */
    public function forCompany(Company|Factory $company): self
    {
        return $this->set('company_id', $company);
    }
}
