<?php

namespace App\Http\Controllers\Backoffice;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Category;
use App\Models\Deposit;
use App\Models\Transaction;
use App\Models\PaymentPlan;
use App\Models\PaymentAccount;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Total Saldo = (APPROVED deposits) + (APPROVED IN transactions) - (APPROVED OUT transactions)
        $approvedDeposits = Deposit::where('status', 'APPROVED')->sum('amount');
        $approvedTransactionsIn = Transaction::where('type', 'IN')->where('status', 'APPROVED')->sum('amount');
        $approvedTransactionsOut = Transaction::where('type', 'OUT')->where('status', 'APPROVED')->sum('amount');
        
        $totalSaldo = $approvedDeposits + $approvedTransactionsIn - $approvedTransactionsOut;
        $totalPengeluaran = $approvedTransactionsOut;
        
        // Total Pending Transaksi (Deposits + Transactions)
        $pendingDepositsCount = Deposit::where('status', 'PENDING')->count();
        $pendingTransactionsCount = Transaction::where('status', 'PENDING')->count();
        $totalPendingTransaksi = $pendingDepositsCount + $pendingTransactionsCount;

        // Anggota belum iuran bulan ini
        $currentMonth = Carbon::now()->format('Y-m');
        $monthlyAmount = PaymentAccount::first()->monthly_amount ?? 100000; // default 100k if not set

        // Users (role = user) who DO NOT have an APPROVED deposit for the current month
        $usersPaidThisMonth = Deposit::where('month', $currentMonth)
            ->where('status', 'APPROVED')
            ->pluck('user_id')
            ->toArray();

        $anggotaBelumIuran = User::where('role', 'user')
            ->whereNotIn('id', $usersPaidThisMonth)
            ->get(['id', 'name', 'email'])
            ->map(function($user) use ($monthlyAmount) {
                return [
                    'nama' => $user->name,
                    'email' => $user->email,
                    'nominal' => $monthlyAmount,
                    'status' => 'Belum Iuran'
                ];
            });

        // Tagihan / Payment Plans (payment_plans)
        $tagihan = PaymentPlan::with(['category', 'user'])
            ->latest()
            ->take(10)
            ->get();

        // Chart Bar: pemasukan dan pengeluaran per bulan (last 6 months)
        $barChartData = [];
        for ($i = 5; $i >= 0; $i--) {
            $monthObj = Carbon::now()->subMonths($i);
            $monthStr = $monthObj->format('Y-m');
            $monthLabel = $monthObj->format('M Y');

            // Pemasukan in this month = approved deposits for this month + approved transactions IN for this month
            $depositsInMonth = Deposit::where('month', $monthStr)
                ->where('status', 'APPROVED')
                ->sum('amount');

            $txInMonth = Transaction::where('type', 'IN')
                ->where('status', 'APPROVED')
                ->whereYear('created_at', $monthObj->year)
                ->whereMonth('created_at', $monthObj->month)
                ->sum('amount');

            $totalIn = $depositsInMonth + $txInMonth;

            // Pengeluaran in this month = approved transactions OUT for this month
            $totalOut = Transaction::where('type', 'OUT')
                ->where('status', 'APPROVED')
                ->whereYear('created_at', $monthObj->year)
                ->whereMonth('created_at', $monthObj->month)
                ->sum('amount');

            $barChartData[] = [
                'label' => $monthLabel,
                'pemasukan' => floatval($totalIn),
                'pengeluaran' => floatval($totalOut)
            ];
        }

        // Chart Pie: total pengeluaran per kategori
        $pieChartData = Transaction::select('categories.name', DB::raw('SUM(transactions.amount) as total'))
            ->join('categories', 'transactions.category_id', '=', 'categories.id')
            ->where('transactions.type', 'OUT')
            ->where('transactions.status', 'APPROVED')
            ->groupBy('categories.name')
            ->get()
            ->map(function($item) {
                return [
                    'name' => $item->name,
                    'total' => floatval($item->total)
                ];
            });

        return view('backoffice.dashboard', compact(
            'totalSaldo',
            'totalPengeluaran',
            'totalPendingTransaksi',
            'anggotaBelumIuran',
            'tagihan',
            'barChartData',
            'pieChartData'
        ));
    }
}
