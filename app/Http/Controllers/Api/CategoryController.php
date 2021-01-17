<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryCollection;
use App\Http\Resources\CategoryResource;
use App\Interfaces\CategoriesRepositoryInterface;
use App\Requests\Categories\StoreRequest;
use App\Requests\Categories\UpdateRequest;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    private $repository;

    public function __construct(CategoriesRepositoryInterface $repository)
    {
        $this->repository = $repository;

        $this->middleware('auth:api', ['except' => ['index', 'show']]);
    }

    public function index(Request $request)
    {
        $categories = $this->repository->get($request->all());

        return new CategoryCollection($categories);
    }

    public function show($id)
    {
        $category = $this->repository->find($id);

        return new CategoryResource($category);
    }

    public function destroy($id)
    {
        $category = $this->repository->delete($id);

        return new CategoryResource($category);
    }

    public function store(StoreRequest $request)
    {
        $category = $this->repository->store($request->getParams()->all());

        return new CategoryResource($category);
    }

    public function update(UpdateRequest $request, $id)
    {
        $category = $this->repository->update($id, $request->getParams()->all());

        return new CategoryResource($category);
    }
}
