<?php

namespace App\Http\Controllers\Service;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SchemeController extends Controller
{
    //
    public function list(Request $request)
    {

        return view('service.scheme.list');

    }
}