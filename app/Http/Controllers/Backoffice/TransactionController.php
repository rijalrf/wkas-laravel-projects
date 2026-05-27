<?php

namespace App\Http\Controllers\Backoffice;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Category;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $query = Transaction::with(['category', 'user']);

        // Keyword search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('reference_number', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhereHas('user', function($qu) use ($search) {
                      $qu->where('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('category', function($qc) use ($search) {
                      $qc->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // Date range filter
        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        // Type filter
        if ($request->filled('type') && in_array($request->type, ['IN', 'OUT'])) {
            $query->where('type', $request->type);
        }

        // Status filter
        if ($request->filled('status') && in_array($request->status, ['PENDING', 'APPROVED', 'REJECTED'])) {
            $query->where('status', $request->status);
        }

        // Creator (Oleh) filter
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Category filter
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        $transactions = $query->latest()->paginate(10);
        $users = User::orderBy('name')->get();
        $categories = Category::orderBy('name')->get();

        return view('backoffice.transactions.index', compact('transactions', 'users', 'categories'));
    }

    public function show(Transaction $transaction)
    {
        $transaction->load(['category', 'user', 'paymentPlan']);
        
        // Return transaction details, Google Drive proxy image path will be generated in the view/JS
        return response()->json($transaction);
    }
}
