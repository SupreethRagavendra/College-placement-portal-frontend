<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            // Email validation removed since email field is disabled in the form
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation()
    {
        // Remove email from the request if it's disabled in form
        if ($this->has('email')) {
            $this->offsetUnset('email');
        }
    }
    
    /**
     * Configure the validator instance.
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            \Log::info('ProfileUpdateRequest validation', [
                'input_data' => $this->all(),
                'user_id' => $this->user()->id ?? null,
                'current_name' => $this->user()->name ?? null,
                'validation_passed' => !$validator->fails()
            ]);
        });
    }
}
