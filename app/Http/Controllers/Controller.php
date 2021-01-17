<?php

namespace App\Http\Controllers;

use App\Interfaces\FormRequestInterface;
use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;

class Controller extends BaseController implements FormRequestInterface
{
    public function __construct(Request $request)
    {
        $this->params = $request->all();
        $this->request = $request;
    }

    public function getParams(): Request
    {
        return $this->request->replace($this->params);
    }
}
