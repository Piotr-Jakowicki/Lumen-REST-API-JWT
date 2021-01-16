<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryCollection;
use App\Models\Category;
use App\Http\Resources\CategoryResource;
use App\Repositories\Categories\CategoriesRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;

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
        $category = Category::findOrFail($id);

        return new CategoryResource($category);
    }

    public function destroy($id)
    {
        $category = Category::findOrFail($id);

        $category->delete();

        return new CategoryResource($category);
    }

    public function store(Request $request)
    {
        $rules = [
            'name' => 'required|string|unique:categories',
            'parent_id' => 'sometimes|integer|nullable'
        ];

        $validated = $this->validate($request, $rules);

        $category = Category::create($validated);

        return new CategoryResource($category);
    }

    public function update(Request $request, $id)
    {
        $category = Category::findOrFail($id);

        $rules = [
            'name' => 'sometimes|required|string|unique:categories',
            'parent_id' => 'sometimes|integer|exists:categories,id'
        ];

        $validated = $this->validate($request, $rules);

        $category->update($validated);

        return new CategoryResource($category);
    }
}
