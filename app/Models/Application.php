<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\ApplicationStatus;
use Carbon\CarbonImmutable;
use Database\Factories\ApplicationFactory;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $company_id
 * @property string $role_title
 * @property string|null $source
 * @property ApplicationStatus $status
 * @property CarbonImmutable|null $applied_at
 * @property string|null $notes
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property-read Company $company
 * @property-read Collection<int, Interaction> $interactions
 */
final class Application extends Model
{
    /** @use HasFactory<ApplicationFactory> */
    use HasFactory;

    protected $fillable = [
        'company_id',
        'role_title',
        'source',
        'status',
        'applied_at',
        'notes',
    ];

    /** @phpstan-return BelongsTo<Company, $this> */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /** @phpstan-return HasMany<Interaction, $this> */
    public function interactions(): HasMany
    {
        return $this->hasMany(Interaction::class);
    }

    protected function casts(): array
    {
        return [
            'status' => ApplicationStatus::class,
            'applied_at' => 'immutable_datetime',
        ];
    }
}
