<?php

namespace App\Http\Controllers\Backoffice;

use App\Http\Controllers\Controller;

class LogController extends Controller
{
    public function index()
    {
        $logUrl = env('SYSTEM_LOGS_URL', 'http://localhost:8888');
        return view('backoffice.logs.index', compact('logUrl'));
    }
}
