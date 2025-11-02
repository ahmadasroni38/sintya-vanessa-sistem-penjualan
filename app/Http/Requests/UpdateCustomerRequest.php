<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCustomerRequest extends FormRequest
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
        $customerId = $this->route('customer')->id;

        return [
            'customer_code' => [
                'nullable',
                'string',
                'max:20',
                Rule::unique('customers', 'customer_code')->ignore($customerId),
            ],
            'customer_name' => 'required|string|max:255',
            'address' => 'nullable|string',
            'phone' => 'nullable|string|max:20',
            'email' => [
                'nullable',
                'email',
                'max:255',
                Rule::unique('customers', 'email')->ignore($customerId),
            ],
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
