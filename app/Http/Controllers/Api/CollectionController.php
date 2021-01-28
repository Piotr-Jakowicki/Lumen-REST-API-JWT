<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CollectionCollection;
use Illuminate\Http\Request;
use App\Models\Collection;

class CollectionController extends Controller
{

    public function __construct()
    {
    }

    public function index(Request $request)
    {
        $collections = Collection::filterBy($request->all())->paginate($request['limit'] ?? 10);

        return new CollectionCollection($collections);
    }

    public function show($id)
    {
    }

    public function destroy($id)
    {
    }

    public function store($request)
    {
    }

    public function update($request, $id)
    {
    }
}
