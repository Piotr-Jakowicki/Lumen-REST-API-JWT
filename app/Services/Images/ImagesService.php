<?php

namespace App\Services\Images;

use App\Interfaces\ImagesRepositoryInterface;
use App\Repositories\Images\ImagesCacheRepository;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ImagesService implements ImagesRepositoryInterface
{
    private $repository;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(ImagesCacheRepository $repository)
    {
        $this->repository = $repository;
    }

    public function get($params)
    {
        return $this->repository->get($params);
    }

    public function find($id)
    {
        return $this->repository->find($id);
    }

    public function store($attributes)
    {
        DB::beginTransaction();

        try {
            $path = Storage::put('public', $attributes['image']);

            $image = $this->repository->store([
                'path' => url() . Storage::url($path),
                'title' => $attributes['title'],
                'user_id' => Auth::id(),
            ]);

            DB::commit();
        } catch (Exception $e) {
            if ($path) {
                Storage::delete($path);
            }

            DB::rollBack();

            // fix
            return $e;
        }

        return $image;
    }

    public function update($id, $attributes)
    {
        $image = $this->repository->find($id);

        DB::beginTransaction();

        try {
            if (isset($attributes['image'])) {
                $oldPath = last(explode('/', $image->path));
                Storage::delete("public/$oldPath");

                $path = Storage::put('public', $attributes['image']);
                $url = Storage::url($path);

                $attributes = array_merge($attributes, ['path' => url() . $url]);
            }

            DB::commit();
        } catch (Exception $e) {
            if ($path) {
                Storage::delete($path);
            }

            DB::rollBack();

            // fix
            return $e;
        }
        return $this->repository->update($id, $attributes);
    }

    public function delete($id)
    {
        return $this->repository->delete($id);
    }
}
