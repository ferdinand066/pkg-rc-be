<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class CreateItemRequest extends FormRequest
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
            'name' => 'required|unique:items',
            'idle_quantity' => 'required|integer|min:0',
            'room_items' => 'array|nullable',
            'room_items.*.room_id' => 'required|exists:rooms,id',
            'room_items.*.quantity' => 'required|integer|min:1',
        ];
    }
}
