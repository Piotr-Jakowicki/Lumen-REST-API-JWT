<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ImageCollection;
use App\Http\Resources\ImageResource;
use App\Interfaces\ImagesRepositoryInterface;
use App\Models\Image;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ImageController extends Controller
{
    private $repository;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(ImagesRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function index(Request $request)
    {
        $images = $this->repository->get($request->all());

        return new ImageCollection($images);
    }

    public function show($id)
    {
        $image = $this->repository->find($id);

        return new ImageResource($image);
    }

    public function destroy($id)
    {
        $image = $this->repository->delete($id);

        return new ImageResource($image);
    }

    public function store(Request $request)
    {
        $rules = [
            'image' => 'required|mimes:jpeg,jpg,png,gif|max:10000',
            'title' => 'required|string|max:100'
        ];

        $this->validate($request, $rules);

        $image = $this->repository->store($request->all());

        return new ImageResource($image);
    }

    public function update(Request $request, $id)
    {
        $rules = [
            'image' => 'sometimes|required|mimes:jpeg,jpg,png,gif|max:10000',
            'title' => 'sometimes|required|string|max:100'
        ];

        $this->validate($request, $rules);

        $image = $this->repository->update($id, $request->except('user_id'));

        return new ImageResource($image);
    }
}
