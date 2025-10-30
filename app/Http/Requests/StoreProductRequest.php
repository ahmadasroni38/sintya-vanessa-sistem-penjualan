<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('create', \App\Models\Product::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'product_code' => [
                'required',
                'string',
                'max:50',
                'unique:products,product_code',
                'regex:/^[A-Z0-9\-]+$/',
            ],
            'product_name' => [
                'required',
                'string',
                'max:255',
            ],
            'description' => [
                'nullable',
                'string',
                'max:1000',
            ],
            'product_type' => [
                'required',
                'string',
                Rule::in(['raw_material', 'finished_goods', 'consumable']),
            ],
            'category_id' => [
                'nullable',
                'integer',
                'exists:product_categories,id',
            ],
            'unit_id' => [
                'required',
                'integer',
                'exists:units,id',
            ],
            'purchase_price' => [
                'required',
                'numeric',
                'min:0',
                'max:999999999999.99',
            ],
            'selling_price' => [
                'nullable',
                'numeric',
                'min:0',
                'max:999999999999.99',
                'gte:purchase_price',
            ],
            'minimum_stock' => [
                'required',
                'integer',
                'min:0',
            ],
            'maximum_stock' => [
                'required',
                'integer',
                'min:0',
                'gte:minimum_stock',
            ],
            'location_id' => [
                'nullable',
                'integer',
                'exists:locations,id',
            ],
            'is_active' => [
                'sometimes',
                'boolean',
            ],
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'product_code' => 'product code',
            'product_name' => 'product name',
            'product_type' => 'product type',
            'category_id' => 'category',
            'unit_id' => 'unit',
            'purchase_price' => 'purchase price',
            'selling_price' => 'selling price',
            'minimum_stock' => 'minimum stock',
            'maximum_stock' => 'maximum stock',
            'location_id' => 'location',
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
            'product_code.regex' => 'The product code must only contain uppercase letters, numbers, and hyphens.',
            'selling_price.gte' => 'The selling price must be greater than or equal to purchase price.',
            'maximum_stock.gte' => 'The maximum stock must be greater than or equal to minimum stock.',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Convert product code to uppercase
        if ($this->has('product_code')) {
            $this->merge([
                'product_code' => strtoupper($this->product_code),
            ]);
        }

        // Ensure boolean values
        if ($this->has('is_active')) {
            $this->merge([
                'is_active' => filter_var($this->is_active, FILTER_VALIDATE_BOOLEAN),
            ]);
        }
    }
}
