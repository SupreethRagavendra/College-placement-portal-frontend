<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class AssessmentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // We're handling authorization in controllers
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $rules = [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'duration' => 'required|integer|min:1|max:300',
            'total_marks' => 'required|integer|min:1',
            'pass_percentage' => 'required|integer|min:1|max:100',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after:start_date',
            'status' => 'required|in:active,inactive,draft',
            'category' => 'required|string|max:100',
            'difficulty_level' => 'required|in:easy,medium,hard',
        ];
        
        // For update requests, make some fields optional
        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            $rules['title'] = 'sometimes|required|string|max:255';
            $rules['duration'] = 'sometimes|required|integer|min:1|max:300';
            $rules['total_marks'] = 'sometimes|required|integer|min:1';
            $rules['pass_percentage'] = 'sometimes|required|integer|min:1|max:100';
            $rules['status'] = 'sometimes|required|in:active,inactive,draft';
            $rules['category'] = 'sometimes|required|string|max:100';
            $rules['difficulty_level'] = 'sometimes|required|in:easy,medium,hard';
        }
        
        return $rules;
    }
    
    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'title.required' => 'The assessment title is required.',
            'duration.required' => 'The duration is required.',
            'duration.min' => 'The duration must be at least 1 minute.',
            'duration.max' => 'The duration cannot exceed 300 minutes.',
            'total_marks.required' => 'The total marks are required.',
            'total_marks.min' => 'The total marks must be at least 1.',
            'pass_percentage.required' => 'The pass percentage is required.',
            'pass_percentage.min' => 'The pass percentage must be at least 1%.',
            'pass_percentage.max' => 'The pass percentage cannot exceed 100%.',
            'end_date.after' => 'The end date must be after the start date.',
            'status.in' => 'The status must be active, inactive, or draft.',
            'difficulty_level.in' => 'The difficulty level must be easy, medium, or hard.',
        ];
    }
}