<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Enums\ApplicationStatus;
use App\Models\Application;
use App\Rules\TransitionToStatusRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;

final class UpdateApplicationRequest extends FormRequest
{
    /** @phpstan-return array<string, array<int, mixed>> */
    public function rules(): array
    {
        return [
            'company_id' => [
                'nullable',
                Rule::exists('companies', 'id'),
            ],
            'role_title' => [
                'nullable',
                'string',
            ],
            'source' => [
                'nullable',
                'string',
            ],
            'status' => [
                'nullable',
                new Enum(ApplicationStatus::class),
                new TransitionToStatusRule($this->application()),
            ],
            'applied_at' => [
                'nullable',
                'date_format:Y-m-d',
            ],
            'notes' => [
                'nullable',
                'string',
            ],
        ];
    }

    public function application(): Application
    {
        $application = $this->route('application');

        if (! ($application instanceof Application)) {
            throw new \RuntimeException('Expected Route Model Binding');
        }

        return $application;
    }
}
