<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCustomerRequest extends FormRequest
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
            'customer_code' => 'nullable|string|max:20|unique:customers,customer_code',
            'customer_name' => 'required|string|max:255',
            'address' => 'nullable|string',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255|unique:customers,email',
            'notes' => 'nullable|string',
            'status' => 'nullable|in:active,inactive',
        ];
    }

    /**
     * Get custom error messages
     */
    public function messages(): array
    {
        return [
            'customer_name.required' => 'Customer name is required',
            'customer_code.unique' => 'Customer code already exists',
            'email.email' => 'Please provide a valid email address',
            'email.unique' => 'Email already exists',
            'status.in' => 'Status must be either active or inactive',
        ];
    }
}
