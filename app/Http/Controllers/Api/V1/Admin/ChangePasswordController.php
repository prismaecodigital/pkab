<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdatePasswordRequest;
use App\Http\Requests\UpdateProfileRequest;
use Gate;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;
use App\Models\Bu;
use App\Models\Dept;
use Illuminate\Http\Response;

class ChangePasswordController extends Controller
{
    public function edit(User $user)
    {
        return response([
            'data' => auth()->user()->load(['roles', 'bu', 'dept']),
            'meta' => [
                'bu'    => Bu::get(),
                'roles' => Role::get(['id', 'title']),
                'dept'    => Dept::get(['id', 'name']),
            ],
        ]);
    }

    public function update(UpdatePasswordRequest $request, User $user)
    {
        auth()->user()->update($request->all());

        return response([
            'data' => auth()->user()->load(['roles', 'bu', 'dept']),
            'meta' => [
                'bu'    => Bu::get(),
                'roles' => Role::get(['id', 'title']),
                'dept'    => Dept::get(['id', 'name']),
            ],
        ]);
    }

    public function updateProfile(UpdateProfileRequest $request)
    {
        $user = auth()->user();

        $user->update($request->validated());

        return (new User($user))
            ->response()
            ->setStatusCode(Response::HTTP_ACCEPTED);
    }

    public function destroy()
    {
        $user = auth()->user();

        $user->update([
            'email' => time() . '_' . $user->email,
        ]);

        $user->delete();

        return redirect()->route('login')->with('message', __('global.delete_account_success'));
    }
}