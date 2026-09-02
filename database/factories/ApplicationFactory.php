<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\ApplicationStatus;
use App\Models\Application;
use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Application>
 */
final class ApplicationFactory extends Factory
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
            'role_title' => $this->faker->jobTitle(),
            'source' => null,
            'status' => ApplicationStatus::APPLIED,
            'applied_at' => null,
            'notes' => null,
        ];
    }

    public function lead(): self
    {
        return $this->set('status', ApplicationStatus::LEAD);
    }

    public function interviewing(): self
    {
        return $this->set('status', ApplicationStatus::INTERVIEWING);
    }

    public function offer(): self
    {
        return $this->set('status', ApplicationStatus::OFFER);
    }

    public function rejected(): self
    {
        return $this->set('status', ApplicationStatus::REJECTED);
    }

    public function withdrawn(): self
    {
        return $this->set('status', ApplicationStatus::WITHDRAWN);
    }

    /** @param Company|Factory<Company> $company */
    public function forCompany(Company|Factory $company): self
    {
        return $this->set('company_id', $company);
    }
}
