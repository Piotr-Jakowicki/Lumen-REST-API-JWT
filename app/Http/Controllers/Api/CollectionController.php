<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CollectionCollection;
use App\Http\Resources\CollectionResource;
use Illuminate\Http\Request;
use App\Models\Collection;

class CollectionController extends Controller
{

    public function __construct()
    {
    }

    public function index(Request $request)
    {
        $collections = Collection::filterBy($request->all())->paginate($request['limit'] ?? 10);

        return new CollectionCollection($collections);
    }

    public function show($id)
    {
        $collection = Collection::findOrFail($id);

        return new CollectionResource($collection);
    }

    public function destroy($id)
    {
        $collection = Collection::findOrFail($id);

        $collection->delete();

        return new CollectionResource($collection);
    }

    public function store($request)
    {
    }

    public function update($request, $id)
    {
    }
}
