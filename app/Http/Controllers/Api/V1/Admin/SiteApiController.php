<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\SiteResource;
use App\Models\Site;
use App\Models\Bu;
use Gate;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class SiteApiController extends Controller
{
    public function busite(Request $request)
    {
        $bus = $request->input('bu');
        if(gettype($bus) == 'array') {
            $sites = Site::whereIn('bu_id', $bus)->orWhere('name','-')->get();
        }
        if(gettype($bus) == 'string') {
            $sites = Site::where('bu_id', $bus)->orWhere('name','-')->get();
        }
        if(!isset($bus)) {
            $sites = [''=> 'Select BU First'];
        }

        return response()->json($sites);
    }

    public function index()
    {
        abort_if(Gate::denies('site_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new SiteResource(Site::with(['bu'])->advancedFilter()->paginate(request('limit', 10)));
    }

    public function store(Request $request)
    {
        $site = Site::create($request->all());

        return (new SiteResource($site))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function create()
    {
        abort_if(Gate::denies('site_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return response([
            'meta' => [
                'bu' => Bu::get(['id', 'name']),
            ],
        ]);
    }

    public function show(Site $site)
    {
        abort_if(Gate::denies('site_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new SiteResource($site);
    }

    public function update(Request $request, Site $site)
    {
        $site->update($request->all());

        return (new SiteResource($site))
            ->response()
            ->setStatusCode(Response::HTTP_ACCEPTED);
    }

    public function edit(Site $site)
    {
        abort_if(Gate::denies('site_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return response([
            'data' => new SiteResource($site->load(['bu'])),
            'meta' => [
                'bu' => Bu::get(['id', 'name']),
            ],
        ]);
    }

    public function destroy(Site $site)
    {
        abort_if(Gate::denies('site_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $site->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}