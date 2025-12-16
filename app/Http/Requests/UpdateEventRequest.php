<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEventRequest extends FormRequest
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
            //
            'title' =>  ["required", "string", "max:255"],
            'desc' => ['required', 'string'],
            'location' => ['nullable', 'string', 'max:255'],
            'date' => ['required', 'date'],
            'available_seats' => ['required', 'integer', 'min:1'],
            'category_id' => ['required', 'exists:categories,id'],
            'images.*' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,svg', 'max:51200'],
        ];
    }
}
