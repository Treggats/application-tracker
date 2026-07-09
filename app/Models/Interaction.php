<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\InteractionType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $application_id
 * @property int|null $contact_id
 * @property InteractionType $type
 * @property \Carbon\CarbonImmutable $occurred_at
 * @property string|null $body
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property-read Application $application
 * @property-read Contact|null $contact
 */
final class Interaction extends Model
{
    protected $fillable = [
        'application_id',
        'contact_id',
        'type',
        'occurred_at',
        'body',
    ];

    /** @phpstan-return BelongsTo<Application, $this> */
    public function application(): BelongsTo
    {
        return $this->belongsTo(Application::class);
    }

    /** @phpstan-return BelongsTo<Contact, $this> */
    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class);
    }

    protected function casts(): array
    {
        return [
            'type' => InteractionType::class,
            'occurred_at' => 'immutable_datetime',
        ];
    }
}
