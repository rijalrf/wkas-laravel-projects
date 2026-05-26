<?php

namespace App\Http\Controllers\Backoffice;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $query = Transaction::with(['category', 'user']);

        if ($request->has('type') && in_array($request->type, ['IN', 'OUT'])) {
            $query->where('type', $request->type);
        }

        if ($request->has('status') && in_array($request->status, ['PENDING', 'APPROVED', 'REJECTED'])) {
            $query->where('status', $request->status);
        }

        $transactions = $query->latest()->get();
        return view('backoffice.transactions.index', compact('transactions'));
    }

    public function show(Transaction $transaction)
    {
        $transaction->load(['category', 'user', 'paymentPlan']);
        
        // Return transaction details, Google Drive proxy image path will be generated in the view/JS
        return response()->json($transaction);
    }
}
