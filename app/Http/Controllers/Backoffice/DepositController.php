<?php

namespace App\Http\Controllers\Backoffice;

use App\Http\Controllers\Controller;
use App\Models\Deposit;
use App\Models\User;
use Illuminate\Http\Request;

class DepositController extends Controller
{
    public function index(Request $request)
    {
        $query = Deposit::with('user');

        // Search keyword
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('reference_number', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhereHas('user', function($qu) use ($search) {
                      $qu->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // Month & Year filter (DB month column is YYYY-MM)
        if ($request->filled('month') && $request->filled('year')) {
            $month = str_pad($request->month, 2, '0', STR_PAD_LEFT);
            $query->where('month', "{$request->year}-{$month}");
        } elseif ($request->filled('year')) {
            $query->where('month', 'like', "{$request->year}-%");
        } elseif ($request->filled('month')) {
            $month = str_pad($request->month, 2, '0', STR_PAD_LEFT);
            $query->where('month', 'like', "%-{$month}");
        }

        // Creator (Oleh) filter
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Status filter
        if ($request->filled('status') && in_array($request->status, ['PENDING', 'APPROVED', 'REJECTED'])) {
            $query->where('status', $request->status);
        }

        $deposits = $query->latest()->paginate(10);
        $users = User::orderBy('name')->get();

        return view('backoffice.deposits.index', compact('deposits', 'users'));
    }
}
