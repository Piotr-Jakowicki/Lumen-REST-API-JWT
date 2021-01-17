<?php

namespace App\Requests\Categories;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class StoreRequest extends Controller
{
    public function __construct(Request $request)
    {
        $rules = [
            'name' => 'required|string|unique:categories',
            'parent_id' => 'sometimes|integer|nullable'
        ];

        $this->validate($request, $rules);

        parent::__construct($request);
    }
}
