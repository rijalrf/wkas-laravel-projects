<?php

namespace App\Http\Controllers\Backoffice;

use App\Http\Controllers\Controller;

class LogController extends Controller
{
    public function index()
    {
        return view('backoffice.logs.index');
    }
}
