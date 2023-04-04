<?php

namespace App\Http\Requests;

use App\Models\Bu;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class UpdateBuRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('bu_edit');
    }

    public function rules()
    {
        return [
            'name' => [
                'string',
                'required',
            ],
            'code' => [
                'string',
                'required',
            ],
        ];
    }
}