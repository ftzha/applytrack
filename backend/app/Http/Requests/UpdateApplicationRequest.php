<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

use App\Enums\ApplicationStatus;
use Illuminate\Validation\Rule;

class UpdateApplicationRequest extends FormRequest
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
            'company_name' => ['sometimes', 'required', 'string', 'max:255'],
            'position' => ['sometimes', 'required', 'string', 'max:255'],
            'location' => ['sometimes', 'nullable', 'string', 'max:255'],
            'employment_type' => ['sometimes', 'nullable', 'string', 'max:100'],
            'work_mode' => ['sometimes', 'nullable', 'string', 'max:100'],

            'salary_min' => [
                'sometimes',
                'nullable',
                'numeric',
                'min:0',
                function (string $attribute, mixed $value, \Closure $fail) {
                    $application = $this->route('application');

                    $salaryMax = $this->has('salary_max')
                        ? $this->input('salary_max')
                        : $application->salary_max;

                    if ($salaryMax !== null && $value !== null && $value > $salaryMax) {
                        $fail('The minimum salary must be less than or equal to the maximum salary.');
                    }
                },
            ],
            'salary_max' => [
                'sometimes',
                'nullable',
                'numeric',
                'min:0',
                function (string $attribute, mixed $value, \Closure $fail) {
                    $application = $this->route('application');

                    $salaryMin = $this->has('salary_min')
                        ? $this->input('salary_min')
                        : $application->salary_min;

                    if ($salaryMin !== null && $value !== null && $value < $salaryMin) {
                        $fail('The maximum salary must be greater than or equal to the minimum salary.');
                    }
                },
            ],
            'currency' => ['sometimes', 'nullable', 'string', 'size:3'],

            'source' => ['sometimes', 'nullable', 'string', 'max:255'],
            'job_url' => ['sometimes', 'nullable', 'url'],

            'status' => [
                'sometimes',
                'required',
                Rule::enum(ApplicationStatus::class),
            ],

            'applied_at' => ['sometimes', 'nullable', 'date'],

            'next_action' => ['sometimes', 'nullable', 'string', 'max:255'],
            'follow_up_at' => ['sometimes', 'nullable', 'date'],

            'notes' => ['sometimes', 'nullable', 'string'],
        ];
    }
}
