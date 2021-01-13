<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryCollection;
use App\Models\Category;
use App\Http\Resources\CategoryResource;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $categories = Category::filterBy($request->all())->paginate($request->limit ?? 10);

        return new CategoryCollection($categories);
    }

    public function show($id)
    {
        $category = Category::find($id);

        if (empty($category)) return response()->json(['error' => 'Category not found!'], 404);

        return new CategoryResource($category);
    }

    public function destroy($id)
    {
        $category = Category::find($id);

        if (empty($category)) return response()->json(['error' => 'Category not found!'], 404);

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
        $category = Category::find($id);

        if (empty($category)) return response()->json(['error' => 'Category not found!'], 404);

        $rules = [
            'name' => 'sometimes|required|string|unique:categories',
            'parent_id' => 'sometimes|integer|exists:categories,id'
        ];

        $validated = $this->validate($request, $rules);

        $category->update($validated);

        return new CategoryResource($category);
    }
}
