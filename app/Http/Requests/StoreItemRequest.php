<?php

namespace App\Http\Requests;

use App\Models\Item;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class StoreItemRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('pkab_item_create');
    }

    public function rules()
    {
        return [
            'name' => [
                'required',
            ],
            'qty' => [
                'required',
            ],
            'items.name' => 'required'
        ];
    }
}