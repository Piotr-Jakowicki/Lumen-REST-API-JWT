<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ImageCollection;
use App\Http\Resources\ImageResource;
use App\Interfaces\CategoryImageRepositoryInterface;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryImageController extends Controller
{
    protected $repository;

    public function __construct(CategoryImageRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function index(Request $request, $id)
    {
        // $images = Category::findOrFail($id)->images;
        // return response()->json($images);
        $images = $this->repository->get($request->all(), $id);

        //return ImageResource::collection($images);

        return new ImageCollection($images);

        $category = Category::findOrFail($id);

        $images = $category->images()->paginate($request->limit ?? 10);
    }
}
