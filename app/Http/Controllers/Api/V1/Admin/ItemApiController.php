<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\ItemResource;
use App\Models\Item;
use Gate;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ItemApiController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('pkab_item_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new ItemResource(Item::advancedFilter());
    }
}