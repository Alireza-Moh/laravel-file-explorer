<?php

namespace Alireza\LaravelFileExplorer\Controllers;

use Illuminate\Routing\Controller;

class BaseController extends Controller
{
    public function __construct()
    {
        $this->middleware("validate.disk");
    }
}
