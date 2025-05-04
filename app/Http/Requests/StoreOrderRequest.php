<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreOrderRequest extends FormRequest
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
            'order_number' => 'required|unique:orders|max:255',
            'order_lines' => 'required|array',
            'order_lines.*' => 'required|array',
            'order_lines.*.sku' => 'required|string|exists:products,sku',
            'order_lines.*.quantity' => 'required|integer|min:1',
            'order_lines.*.price' => 'required|numeric|min:0',
        ];
    }
}
