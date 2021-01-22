<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ImageCollection;
use App\Http\Resources\ImageResource;
use App\Models\Image;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ImageController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function index(Request $request)
    {
        $images = Image::filterBy($request->all())->paginate($request['limit'] ?? 10);

        return new ImageCollection($images);
    }

    public function show($id)
    {
        $image = Image::findOrFail($id);

        return new ImageResource($image);
    }

    public function destroy($id)
    {
        $image = Image::findOrFail($id);

        $image->delete();

        return new ImageResource($image);
    }

    public function store(Request $request)
    {
        $path = Storage::put('public', $request->image);
        $url = Storage::url($path);

        $data = [
            'path' => url() . $url,
            'title' => $request->title,
            'user_id' => Auth::id(),
        ];

        $image = Image::create($data);

        return new ImageResource($image);
    }

    public function update(Request $request, $id)
    {
        $image = Image::findOrFail($id);

        if ($request->hasFile('image')) {
            $oldPath = last(explode('/', $image->path));
            Storage::delete("public/$oldPath");

            $path = Storage::put('public', $request->image);
            $url = Storage::url($path);

            $newPath = ['path' => url() . $url];
        }


        if (isset($newPath)) {
            $request = array_merge($request->only('title'), $newPath);
        } else {
            $request = $request->only('title');
        }

        $image->update($request);

        //return $newPath;
        return new ImageResource($image);
    }
}
