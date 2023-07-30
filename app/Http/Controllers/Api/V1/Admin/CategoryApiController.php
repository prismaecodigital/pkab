<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\Category;
use App\Http\Resources\Admin\CategoryResource;
use Gate;

class CategoryApiController extends Controller
{
    
    public function index()
    {
        abort_if(Gate::denies('marketlistItem_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new CategoryResource(Category::advancedFilter()->paginate(request('limit', 10)));
    }

    public function create()
    {
        abort_if(Gate::denies('marketlistItem_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return response([
            'meta' => [],
        ]);
    }

    public function store(Request $request, Category $category)
    {
        $category = Category::create($request->all());

        return (new CategoryResource($category))
        ->response()
        ->setStatusCode(Response::HTTP_ACCEPTED);
    }

    public function edit(Category $category)
    {
        abort_if(Gate::denies('marketlistItem_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        // edit item
        return response([
            'meta' => [
                'data'       => new CategoryResource($category),
                'category'   => Category::get(['id', 'name']),
            ],
        ]);
    }

    public function update(Request $request, Category $category)
    {
        abort_if(Gate::denies('marketlistItem_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $category->update($request->all());

        return (new CategoryResource($category))
        ->response()
        ->setStatusCode(Response::HTTP_ACCEPTED);
    }

    public function destroy(Category $category)
    {
        // delete

        abort_if(Gate::denies('marketlistItem_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $category->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
