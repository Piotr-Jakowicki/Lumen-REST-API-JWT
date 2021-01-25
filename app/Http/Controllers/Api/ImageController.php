<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ImageCollection;
use App\Http\Resources\ImageResource;
use App\Interfaces\ImagesRepositoryInterface;
use App\Requests\Images\StoreRequest;
use App\Requests\Images\UpdateRequest;
use Illuminate\Http\Request;

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

    public function store(StoreRequest $request)
    {
        $image = $this->repository->store($request->getParams()->all());

        return new ImageResource($image);
    }

    public function update(UpdateRequest $request, $id)
    {
        $image = $this->repository->update($id, $request->getParams()->except('user_id'));

        return new ImageResource($image);
    }
}
