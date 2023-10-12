<?php

namespace App\Http\Requests;

use App\Models\Marketlist;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class StoreMarketlistRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('marketlist_create');
    }

    public function rules()
    {
        return [
            'bu_id' => [
                'required',
            ],
            'event' => [
                'required',
            ],
            'site_id' => [
                'required'
            ],
            'items' => [
                'required', // Make sure 'items' is present
                'array', // Ensure 'items' is an array
            ],
            'items.*.required_date' => [
                'required',
            ],
            'items.*.item_id' => [
                'required',
            ],
            'items.*.qty' => [
                'required',
            ],
            'items.*.satuan' => [
                'required',
            ],
        ];
    }
}