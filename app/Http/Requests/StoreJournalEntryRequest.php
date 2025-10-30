<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreJournalEntryRequest extends FormRequest
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
        return [
            'entry_date' => 'required|date|before_or_equal:today',
            'reference_number' => 'nullable|string|max:50',
            'description' => 'required|string|max:1000',
            'entry_type' => 'required|in:general,adjustment,closing,opening',
            'details' => 'required|array|min:2',
            'details.*.account_id' => 'required|exists:chart_of_accounts,id',
            'details.*.transaction_type' => 'required|in:debit,credit',
            'details.*.amount' => 'required|numeric|min:0.01',
            'details.*.description' => 'nullable|string|max:500',
            'details.*.quantity' => 'nullable|numeric|min:0',
            'details.*.unit_price' => 'nullable|numeric|min:0',
            'details.*.tax_rate' => 'nullable|numeric|min:0|max:100',
            'details.*.tax_amount' => 'nullable|numeric|min:0',
            'details.*.department_id' => 'nullable|exists:locations,id',
            'details.*.project_id' => 'nullable|exists:products,id',
            'details.*.reconciliation_id' => 'nullable|string|max:50',
            'currency' => 'nullable|string|size:3|in:IDR,USD,EUR,GBP',
            'exchange_rate' => 'nullable|numeric|min:0.0001|max:9999.9999',
            'metadata' => 'nullable|array',
        ];
    }

    /**
     * Get custom error messages for validation attributes.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'entry_date.required' => 'Entry date is required.',
            'entry_date.before_or_equal' => 'Entry date cannot be in the future.',
            'reference_number.max' => 'Reference number cannot exceed 50 characters.',
            'description.required' => 'Description is required.',
            'description.max' => 'Description cannot exceed 1000 characters.',
            'entry_type.required' => 'Entry type is required.',
            'entry_type.in' => 'Entry type must be one of: general, adjustment, closing, opening.',
            'details.required' => 'At least 2 journal entry details are required.',
            'details.min' => 'At least 2 journal entry details are required.',
            'details.*.account_id.required' => 'Account is required for all journal entry details.',
            'details.*.account_id.exists' => 'Selected account is invalid.',
            'details.*.transaction_type.required' => 'Transaction type is required for all journal entry details.',
            'details.*.transaction_type.in' => 'Transaction type must be either debit or credit.',
            'details.*.amount.required' => 'Amount is required for all journal entry details.',
            'details.*.amount.numeric' => 'Amount must be a number.',
            'details.*.amount.min' => 'Amount must be at least 0.01.',
            'details.*.description.max' => 'Description cannot exceed 500 characters.',
            'details.*.quantity.numeric' => 'Quantity must be a number.',
            'details.*.quantity.min' => 'Quantity must be at least 0.',
            'details.*.unit_price.numeric' => 'Unit price must be a number.',
            'details.*.unit_price.min' => 'Unit price must be at least 0.',
            'details.*.tax_rate.numeric' => 'Tax rate must be a number.',
            'details.*.tax_rate.min' => 'Tax rate must be at least 0.',
            'details.*.tax_rate.max' => 'Tax rate cannot exceed 100%.',
            'details.*.tax_amount.numeric' => 'Tax amount must be a number.',
            'details.*.tax_amount.min' => 'Tax amount must be at least 0.',
            'details.*.department_id.exists' => 'Selected department is invalid.',
            'details.*.project_id.exists' => 'Selected project is invalid.',
            'details.*.reconciliation_id.max' => 'Reconciliation ID cannot exceed 50 characters.',
            'currency.size' => 'Currency must be 3 characters.',
            'currency.in' => 'Currency must be one of: IDR, USD, EUR, GBP.',
            'exchange_rate.numeric' => 'Exchange rate must be a number.',
            'exchange_rate.min' => 'Exchange rate must be at least 0.0001.',
            'exchange_rate.max' => 'Exchange rate cannot exceed 9999.9999.',
            'metadata.array' => 'Metadata must be an array.',
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
            'entry_date' => 'entry date',
            'reference_number' => 'reference number',
            'description' => 'description',
            'entry_type' => 'entry type',
            'details' => 'journal entry details',
            'details.*.account_id' => 'account',
            'details.*.transaction_type' => 'transaction type',
            'details.*.amount' => 'amount',
            'details.*.description' => 'description',
            'details.*.quantity' => 'quantity',
            'details.*.unit_price' => 'unit price',
            'details.*.tax_rate' => 'tax rate',
            'details.*.tax_amount' => 'tax amount',
            'details.*.department_id' => 'department',
            'details.*.project_id' => 'project',
            'details.*.reconciliation_id' => 'reconciliation ID',
            'currency' => 'currency',
            'exchange_rate' => 'exchange rate',
            'metadata' => 'metadata',
        ];
    }

    /**
     * Configure the validator instance.
     *
     * @param \Illuminate\Validation\Validator $validator
     * @return void
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            // Validate that total debit equals total credit
            $details = $this->input('details', []);
            $totalDebit = 0;
            $totalCredit = 0;

            foreach ($details as $index => $detail) {
                if ($detail['transaction_type'] === 'debit') {
                    $totalDebit += $detail['amount'];
                } else {
                    $totalCredit += $detail['amount'];
                }
            }

            if (abs($totalDebit - $totalCredit) > 0.01) {
                $validator->errors()->add('details', 'Total debit must equal total credit. Difference: ' .
                    number_format(abs($totalDebit - $totalCredit), 2));
            }

            // Validate that each detail has either debit or credit amount > 0
            foreach ($details as $index => $detail) {
                $amount = $detail['transaction_type'] === 'debit'
                    ? $detail['debit_amount']
                    : $detail['credit_amount'];

                if ($amount <= 0) {
                    $validator->errors()->add("details.{$index}.amount",
                        'Amount must be greater than 0 for ' .
                        ucfirst($detail['transaction_type']) . ' entries.');
                }
            }
        });
    }
}
