<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class StoreApplicationRequest extends FormRequest
{
    /** @phpstan-return array<string, array<int, mixed>> */
    public function rules(): array
    {
        return [
            'company_id' => [
                'required',
                Rule::exists('companies', 'id'),
            ],
            'role_title' => [
                'required',
                'string',
            ],
            'source' => [
                'nullable',
                'string',
            ],
            'applied_at' => [
                'required',
                'date_format:Y-m-d',
            ],
            'notes' => [
                'nullable',
                'string',
            ],
        ];
    }
}
