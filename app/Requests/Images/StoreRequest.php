<?php

namespace App\Requests\Images;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class StoreRequest extends Controller
{
    public function __construct(Request $request)
    {
        $rules = [
            'image' => 'required|mimes:jpeg,jpg,png,gif|max:10000',
            'title' => 'required|string|max:100',
            'categories.*' => 'integer|exists:categories,id'
        ];

        $this->validate($request, $rules);

        parent::__construct($request);
    }
}
