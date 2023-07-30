<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\MarketlistItem;
use App\Models\Category;
use App\Http\Resources\Admin\MarketlistItemResource;
use Gate;
use Carbon\Carbon;

class MarketlistItemApiController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
    */

    public function index()
    {
        abort_if(Gate::denies('marketlistItem_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new MarketlistItemResource(MarketlistItem::with(['category'])->advancedFilter()->paginate(request('limit', 10)));
    }

    public function create()
    {
        abort_if(Gate::denies('marketlistItem_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return response([
            'meta' => [
                'category'   => Category::get(['id', 'name']),
            ],
        ]);
    }

    public function store(Request $request, MarketlistItem $marketlistItem)
    {
        $marketlistItem = MarketlistItem::create($request->all());

        return (new MarketlistItemResource($marketlistItem))
        ->response()
        ->setStatusCode(Response::HTTP_ACCEPTED);
    }

    public function show(MarketlistItem $marketlistItem)
    {
        // abort if gate denies
        return new MarketlistItemResource($marketlistItem->load(['category']));
    }

    public function edit(MarketlistItem $marketlistItem)
    {
        // abort_if(Gate::denies('marketlistItem_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        // edit item
        return response([
            'data'       => new MarketlistItemResource($marketlistItem),
            'meta' => [
                'category'   => Category::get(['id', 'name']),
            ],
        ]);
    }

    public function update(Request $request, MarketlistItem $marketlistItem)
    {
        abort_if(Gate::denies('marketlistItem_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $marketlistItem->update($request->all());

        return (new MarketlistItemResource($marketlistItem))
        ->response()
        ->setStatusCode(Response::HTTP_ACCEPTED);
    }

    public function destroy(MarketlistItem $marketlistItem)
    {
        // delete

        abort_if(Gate::denies('marketlistItem_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $marketlistItem->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
