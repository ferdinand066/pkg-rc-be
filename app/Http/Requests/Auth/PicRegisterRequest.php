<?php

namespace App\Http\Requests\Auth;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class PicRegisterRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        $rules = [
            'name' => 'required|string|max:255',
            'password' => 'required|min:8',
            'email' => 'required|unique:users,email',
            'address' => 'nullable|string',
            'picture_path' => 'nullable|image',
        ];

        return $this->addAdditionalRules($rules);
    }

    /**
     * Add additional rule if the requested user has a role outside the parent.
     * If
     * @param array $rules
     * @return array
     */
    private function addAdditionalRules(array $rules){
        $rules['nickname'] = 'required|string';
        $rules['dob'] = 'required|date';
        $rules['phone_number'] = 'required|numeric';
        $rules['parish_id'] = 'required|exists:parishes,id';
        $rules['occupation'] = 'required|string';
        // $rules['shift'] = 'required|array';
        // $rules['grade_id'] = 'required|array';
        // foreach ($this->grade_id as $key => $grade_id) {
        //     $rules['grade_id.' . $key] = 'required|in:' . implode(',', array_keys(config('constants.grades')));
        // }

        return $rules;
    }
}
