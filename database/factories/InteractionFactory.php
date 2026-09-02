<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\InteractionType;
use App\Models\Application;
use App\Models\Contact;
use App\Models\Interaction;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Interaction>
 */
final class InteractionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'application_id' => Application::factory(),
            'contact_id' => null,
            'type' => InteractionType::NOTE,
            'occurred_at' => $this->faker->dateTimeBetween('-30 days'),
            'body' => $this->faker->sentence(),
        ];
    }

    public function email(): self
    {
        return $this->set('type', InteractionType::EMAIL);
    }

    public function call(): self
    {
        return $this->set('type', InteractionType::CALL);
    }

    public function interview(): self
    {
        return $this->set('type', InteractionType::INTERVIEW);
    }

    /** @param Application|Factory<Application> $application */
    public function forApplication(Application|Factory $application): self
    {
        return $this->set('application_id', $application);
    }

    /** @param Contact|Factory<Contact> $contact */
    public function forContact(Contact|Factory $contact): self
    {
        return $this->set('contact_id', $contact);
    }
}
