<?php

namespace App\Http\Requests;

use App\Models\Dept;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class StoreDeptRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('dept_create');
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
            'bu_id' => [
                'integer',
                'exists:bus,id',
                'required',
            ],
        ];
    }
}