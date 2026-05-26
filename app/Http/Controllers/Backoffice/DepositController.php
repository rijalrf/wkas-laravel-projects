<?php

namespace App\Http\Controllers\Backoffice;

use App\Http\Controllers\Controller;
use App\Models\Deposit;
use Illuminate\Http\Request;

class DepositController extends Controller
{
    public function index(Request $request)
    {
        $query = Deposit::with('user');

        if ($request->has('status') && in_array($request->status, ['PENDING', 'APPROVED', 'REJECTED'])) {
            $query->where('status', $request->status);
        }

        $deposits = $query->latest()->get();
        return view('backoffice.deposits.index', compact('deposits'));
    }
}
