<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CollectionCollection;
use App\Http\Resources\CollectionResource;
use Illuminate\Http\Request;
use App\Models\Collection;
use Illuminate\Support\Facades\Auth;

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

    public function store(Request $request)
    {
        $rules = [
            'name' => 'required'
        ];

        $this->validate($request, $rules);

        $data = [
            'user_id' => Auth::id()
        ];

        $collection = Collection::create(
            array_merge($request->only(['name']), $data)
        );

        return new CollectionResource($collection);
    }

    public function update(Request $request, $id)
    {
        $collection = Collection::findOrFail($id);

        $collection->update($request->all());

        return new CollectionResource($collection);
    }
}
