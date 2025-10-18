<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class QuestionRequest extends FormRequest
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
            'question_text' => 'required|string',
            'question_type' => 'required|in:mcq,true_false,short_answer',
            'correct_answer' => 'required|string',
            'marks' => 'required|integer|min:1',
            'difficulty_level' => 'required|in:easy,medium,hard',
            'order' => 'required|integer|min:0',
        ];
        
        // For MCQ questions, validate options
        if ($this->input('question_type') === 'mcq') {
            $rules['options'] = 'required|array|min:2';
            $rules['options.*'] = 'required|string';
        }
        
        // For update requests, make some fields optional
        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            $rules['question_text'] = 'sometimes|required|string';
            $rules['question_type'] = 'sometimes|required|in:mcq,true_false,short_answer';
            $rules['correct_answer'] = 'sometimes|required|string';
            $rules['marks'] = 'sometimes|required|integer|min:1';
            $rules['difficulty_level'] = 'sometimes|required|in:easy,medium,hard';
            $rules['order'] = 'sometimes|required|integer|min:0';
            
            // For MCQ questions on update
            if ($this->input('question_type') === 'mcq') {
                $rules['options'] = 'sometimes|required|array|min:2';
                $rules['options.*'] = 'sometimes|required|string';
            }
        }
        
        return $rules;
    }
    
    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'question_text.required' => 'The question text is required.',
            'question_type.required' => 'The question type is required.',
            'question_type.in' => 'The question type must be MCQ, True/False, or Short Answer.',
            'correct_answer.required' => 'The correct answer is required.',
            'marks.required' => 'The marks are required.',
            'marks.min' => 'The marks must be at least 1.',
            'difficulty_level.in' => 'The difficulty level must be easy, medium, or hard.',
            'order.min' => 'The order must be at least 0.',
            'options.required' => 'MCQ questions must have at least 2 options.',
            'options.min' => 'MCQ questions must have at least 2 options.',
        ];
    }
}