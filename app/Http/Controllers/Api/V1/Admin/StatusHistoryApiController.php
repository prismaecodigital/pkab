<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\StatusHistoryResource;
use App\Models\StatusHistory;
use Gate;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class StatusHistoryApiController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('status_history_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new StatusHistoryResource(StatusHistory::advancedFilter());
    }

    public function destroy(StatusHistory $statusHistory)
    {
        abort_if(Gate::denies('status_history_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $statusHistory->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}