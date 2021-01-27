<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Collection;

class CollectionController extends Controller
{

    public function __construct()
    {
    }

    public function index(Request $request)
    {
        return response()->json(['data' => Collection::all()]);
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
