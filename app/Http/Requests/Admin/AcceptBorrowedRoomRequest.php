<?php

namespace App\Http\Requests\Admin;

use App\Models\BorrowedRoom;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class AcceptBorrowedRoomRequest extends FormRequest
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
            'start_borrowing_time' => 'required|date_format:H:i',
        ];
    }

    /**
     * Configure the validator instance.
     *
     * @param  \Illuminate\Validation\Validator  $validator
     * @return void
     */
    protected function withValidator(Validator $validator): void
    {
        $validator->after(function ($validator) {
            $borrowedRoom = BorrowedRoom::where('id', $this->route('borrowed_room'))->first();

            if ($borrowedRoom && $this->input('start_borrowing_time')) {
                $startBorrowingTime = $this->input('start_borrowing_time');
                $startEventTime = $borrowedRoom->start_event_time;

                if (strtotime($startBorrowingTime) >= strtotime($startEventTime)) {
                    $validator->errors()->add('start_borrowing_time', 'The start borrowing time must be before the start event time.');
                }
            }
        });
    }
}
