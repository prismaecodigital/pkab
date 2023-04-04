<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBuRequest;
use App\Http\Requests\UpdateBuRequest;
use App\Http\Resources\Admin\BuResource;
use App\Models\Bu;
use Gate;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class BuApiController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('bu_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new BuResource(Bu::advancedFilter());
    }

    public function store(StoreBuRequest $request)
    {
        $bu = Bu::create($request->validated());

        return (new BuResource($bu))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function create()
    {
        abort_if(Gate::denies('bu_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return response([
            'meta' => [],
        ]);
    }

    public function show(Bu $bu)
    {
        abort_if(Gate::denies('bu_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new BuResource($bu);
    }

    public function update(UpdateBuRequest $request, Bu $bu)
    {
        $bu->update($request->validated());

        return (new BuResource($bu))
            ->response()
            ->setStatusCode(Response::HTTP_ACCEPTED);
    }

    public function edit(Bu $bu)
    {
        abort_if(Gate::denies('bu_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return response([
            'data' => new BuResource($bu),
            'meta' => [],
        ]);
    }

    public function destroy(Bu $bu)
    {
        abort_if(Gate::denies('bu_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $bu->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}