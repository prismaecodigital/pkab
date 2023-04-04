<?php

namespace App\Http\Requests;

use App\Models\PkabItem;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class StorePkabItemRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('pkab_item_create');
    }

    public function rules()
    {
        return [
            'req_date' => [
                'date_format:' . config('project.date_format'),
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