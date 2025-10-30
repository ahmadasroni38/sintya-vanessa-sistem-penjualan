<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateChartOfAccountRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Handle authorization via middleware/policies
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $accountId = $this->route('chartOfAccount');

        return [
            'account_code' => 'sometimes|required|string|max:20|unique:chart_of_accounts,account_code,' . $accountId->account_code,
            'account_name' => 'sometimes|required|string|max:255',
            'account_type' => 'sometimes|required|in:asset,liability,equity,revenue,expense',
            'normal_balance' => 'sometimes|required|in:debit,credit',
            'parent_id' => [
                'sometimes',
                'nullable',
                'exists:chart_of_accounts,id',
                function ($attribute, $value, $fail) use ($accountId) {
                    if ($value == $accountId->id) {
                        $fail('An account cannot be its own parent.');
                    }
                },
            ],
            'level' => 'sometimes|required|integer|min:1|max:5',
            'description' => 'sometimes|nullable|string',
            'opening_balance' => 'sometimes|nullable|numeric',
            'is_active' => 'sometimes|boolean',
        ];
    }

    /**
     * Get custom attribute names for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'account_code' => 'account code',
            'account_name' => 'account name',
            'account_type' => 'account type',
            'normal_balance' => 'normal balance',
            'parent_id' => 'parent account',
            'opening_balance' => 'opening balance',
            'is_active' => 'active status',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'account_code.required' => 'The account code is required.',
            'account_code.unique' => 'This account code already exists.',
            'account_type.in' => 'The account type must be one of: asset, liability, equity, revenue, or expense.',
            'normal_balance.in' => 'The normal balance must be either debit or credit.',
            'level.min' => 'The level must be at least 1.',
            'level.max' => 'The level cannot exceed 5.',
        ];
    }
}
