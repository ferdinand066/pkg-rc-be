<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class CreateBorrowedRecurringRoomRequest extends FormRequest
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
            'event_name' => 'required|string',
            'recurring' => 'required|string|in:daily,weekly,monthly',
            'pic_name' => 'required|string',
            'pic_phone_number' => 'required|string',
            'start_borrowed_date' => 'required|date|after_or_equal:today',
            'end_borrowed_date' => 'required|date|after_or_equal:start_borrowed_date',
            'capacity' => 'required|integer',
            'room_id' => 'required|exists:rooms,id',
            'start_borrowing_time' => 'required|date_format:H:i',
            'start_event_time' => 'required|date_format:H:i|after_or_equal:start_borrowing_time',
            'end_event_time' => 'required|date_format:H:i|after:start_event_time',
            'description' => 'required|string',
            'items' => 'array|nullable',
            'items.*.item_id' => 'required|exists:items,id',
            'items.*.quantity' => 'required|integer|min:1',
        ];
    }
}
