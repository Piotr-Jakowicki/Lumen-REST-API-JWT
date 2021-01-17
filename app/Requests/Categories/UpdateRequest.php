<?php

namespace App\Requests\Categories;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UpdateRequest extends Controller
{
    public function __construct(Request $request)
    {
        $rules = [
            'name' => 'sometimes|required|string|unique:categories',
            'parent_id' => 'sometimes|integer|exists:categories,id'
        ];

        $this->validate($request, $rules);

        parent::__construct($request);
    }
}
