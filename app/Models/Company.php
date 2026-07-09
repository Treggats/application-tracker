<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Testing\Fluent\Concerns\Has;

/**
 * @property int $id
 * @property string $name
 * @property string|null $kvk_number
 * @property string|null $website
 * @property string|null $city
 * @property string|null $sbi_code
 * @property string|null $sbi_description
 * @property \Carbon\CarbonImmutable|null $enriched_at
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Application> $applications
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Contact> $contacts
 */
final class Company extends Model
{
    protected $fillable = [
        'name',
        'kvk_number',
        'website',
        'city',
        'sbi_code',
        'sbi_description',
        'enriched_at',
    ];

    /** @phpstan-return HasMany<Application, $this> */
    public function applications(): HasMany
    {
        return $this->hasMany(Application::class);
    }

    /** @phpstan-return HasMany<Contact, $this> */
    public function contacts(): HasMany
    {
        return $this->hasMany(Contact::class);
    }

    protected function casts(): array
    {
        return [
            'enriched_at' => 'immutable_datetime',
        ];
    }
}
