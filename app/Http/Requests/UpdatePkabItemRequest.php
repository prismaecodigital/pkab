<?php

namespace App\Http\Requests;

use App\Models\PkabItem;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class UpdatePkabItemRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'req_date' => [
                'string',
                'required',
            ],
            'dept_id' => [
                'integer',
                'exists:depts,id',
                'nullable',
            ],
        ];
    }
}