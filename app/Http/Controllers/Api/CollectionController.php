<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CollectionCollection;
use App\Http\Resources\CollectionResource;
use App\Interfaces\CollectionsRepositoryInterface;
use Illuminate\Http\Request;
use App\Models\Collection;
use Illuminate\Support\Facades\Auth;

class CollectionController extends Controller
{
    protected $repository;

    public function __construct(CollectionsRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function index(Request $request)
    {
        $collections = $this->repository->get($request->all());

        return new CollectionCollection($collections);
    }

    public function show($id)
    {
        $collection = $this->repository->find($id);

        return new CollectionResource($collection);
    }

    public function destroy($id)
    {
        $collection = $this->repository->delete($id);

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


        $collection = $this->repository->store(array_merge($request->only(['name']), $data));

        return new CollectionResource($collection);
    }

    public function update(Request $request, $id)
    {
        $rules = [
            'name' => 'required'
        ];

        $this->validate($request, $rules);

        $collection = $this->repository->update($id, $request->only(['name']));

        return new CollectionResource($collection);
    }
}
