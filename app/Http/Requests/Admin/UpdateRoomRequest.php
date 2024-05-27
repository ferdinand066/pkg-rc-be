<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateRoomRequest extends FormRequest
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
        $roomId = $this->route('room');

        return [
            'name' => ['required', Rule::unique('rooms')->ignore($roomId)],
            'floor_id' => 'required|exists:floors,id',
            'item_id' => 'required|array',
            'item_id.*' => 'required|exists:items,id',
        ];
    }
}
