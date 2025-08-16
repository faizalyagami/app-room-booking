<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

use Carbon\Carbon;

class MyBookingAlatTestListRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [
            'date' => 'required|date_format:Y-m-d|after_or_equal:today',
            'time_start.*' => 'required',
            'time_end.*' => 'required',
            'purpose' => 'required|string|max:100',
            'items' => 'required',
        ];

        return $rules;
    }
}
