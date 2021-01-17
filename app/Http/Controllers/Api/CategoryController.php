<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryCollection;
use App\Models\Category;
use App\Http\Resources\CategoryResource;
use App\Repositories\Categories\CategoriesRepositoryInterface;
use App\Requests\Categories\StoreRequest;
use App\Requests\Categories\UpdateRequest;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    private $repository;

    public function __construct(CategoriesRepositoryInterface $repository)
    {
        $this->repository = $repository;
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
        $category = $this->repository->find($id);

        $category->delete();

        return new CategoryResource($category);
    }

    public function store(StoreRequest $request)
    {
        $category = Category::create($request->getParams()->all());

        return new CategoryResource($category);
    }

    public function update(UpdateRequest $request, $id)
    {
        $category = $this->repository->find($id);

        $category->update($request->getParams()->all());

        return new CategoryResource($category);
    }
}
