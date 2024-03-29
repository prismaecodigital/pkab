<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\Admin\UserResource;
use App\Models\Role;
use App\Models\User;
use App\Models\Bu;
use App\Models\Dept;
use Gate;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class UsersApiController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('user_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new UserResource(User::with(['roles','dept.bu'])->advancedFilter()->paginate(request('limit', 10)));
    }

    public function store(StoreUserRequest $request)
    {
        $user = User::create($request->validated());
        $user->roles()->sync($request->input('roles.*.id', []));
        $user->dept()->sync($request->input('dept.*.id', []));
        $user->bu()->sync($request->input('bu.*.id', []));

        return (new UserResource($user))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function create()
    {
        abort_if(Gate::denies('user_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return response([
            'meta' => [
                'roles' => Role::get(['id', 'title']),
                'bu'    => Bu::get(),
                'dept'  => Dept::get()
            ],
        ]);
    }

    public function show(User $user)
    {
        abort_if(Gate::denies('user_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new UserResource($user->load(['roles','dept.bu']));
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        $user->update($request->validated());
        $user->roles()->sync($request->input('roles.*.id', []));
        $user->dept()->sync($request->input('dept.*.id', []));
        $user->bu()->sync($request->input('bu.*.id', []));


        return (new UserResource($user))
            ->response()    
            ->setStatusCode(Response::HTTP_ACCEPTED);
    }

    public function edit(User $user)
    {
        abort_if(Gate::denies('user_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return response([
            'data' => new UserResource($user->load(['roles', 'dept','bu'])),
            'meta' => [
                'bu'    => Bu::get(),
                'roles' => Role::get(['id', 'title']),
                'dept'    => Dept::whereIn('bu_id', $user->bu->pluck('id'))->get(['id', 'name']),
            ],
        ]);
    }

    public function destroy(User $user)
    {
        abort_if(Gate::denies('user_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $user->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }

    public function getSurvey()
    {
        return response()->json([
            'survey' => [
                'has_completed_survey' => auth()->user()->has_completed_survey,
                'link'  => 'https://forms.gle/Jy6Yb6fw5xMMTugv9'
            ]
        ]);
    }

    public function updateUserSurvey()
    {
        $user = auth()->user();
        $user->has_completed_survey = true;
        $user->save();

        dd($user);

        return response()->json('success');
    }
}