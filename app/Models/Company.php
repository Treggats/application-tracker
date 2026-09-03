<?php

declare(strict_types=1);

namespace App\Models;

use Carbon\CarbonImmutable;
use Database\Factories\CompanyFactory;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $name
 * @property string|null $kvk_number
 * @property string|null $website
 * @property string|null $city
 * @property string|null $sbi_code
 * @property string|null $sbi_description
 * @property CarbonImmutable|null $enriched_at
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property-read Collection<int, Application> $applications
 * @property-read Collection<int, Contact> $contacts
 */
final class Company extends Model
{
    /** @use HasFactory<CompanyFactory> */
    use HasFactory;

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
