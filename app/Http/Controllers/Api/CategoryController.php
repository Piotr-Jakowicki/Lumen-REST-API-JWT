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
}
