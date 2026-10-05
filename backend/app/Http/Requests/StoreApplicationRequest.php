<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

use App\Enums\ApplicationStatus;
use Illuminate\Validation\Rule;

class StoreApplicationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'company_name' => ['required', 'string', 'max:255'],
            'position' => ['required', 'string', 'max:255'],
            'location' => ['nullable', 'string', 'max:255'],
            'employment_type' => ['nullable', 'string', 'max:100'],
            'work_mode' => ['nullable', 'string', 'max:100'],

            'salary_min' => ['nullable', 'numeric', 'min:0'],
            'salary_max' => ['nullable', 'numeric', 'min:0', 'gte:salary_min'], //salary_max CANNOT LOWER THAN salary_min
            'currency' => ['nullable', 'string', 'size:3'],

            'source' => ['nullable', 'string', 'max:255'],
            'job_url' => ['nullable', 'url'],

            'status' => [
                'required',
                Rule::enum(ApplicationStatus::class)
            ], //Allows what listed in ApplicationStatus.php

            'applied_at' => ['nullable', 'date'],

            'next_action' => [
                'nullable',
                'string',
                'max:255',
            ],

            'follow_up_at' => [
                'nullable',
                'date',
            ],
            
            'notes' => ['nullable', 'string'],
        ];
    }
}
