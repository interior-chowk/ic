<?php

namespace App\Http\Controllers\Service;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MyshopController extends Controller
{
    //
    public function list(Request $request)
    {

        return view('service.myshop.list');

    }
}