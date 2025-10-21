<?php

namespace Modules\Website\Http\Controllers;

use Illuminate\Routing\Controller;

class WebsiteController extends Controller
{
    public function index()
    {
        return view('website::index');
    }
}
