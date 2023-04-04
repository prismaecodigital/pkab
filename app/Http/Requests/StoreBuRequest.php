<?php

namespace App\Http\Requests;

use App\Models\Bu;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class StoreBuRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('bu_create');
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