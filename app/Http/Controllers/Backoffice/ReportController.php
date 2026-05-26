<?php

namespace App\Http\Controllers\Backoffice;

use App\Http\Controllers\Controller;
use App\Models\Deposit;
use App\Models\Transaction;
use App\Models\Category;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $categories = Category::all();
        
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $categoryId = $request->input('category_id');
        $status = $request->input('status');
        $type = $request->input('type'); // IN or OUT

        // If no filter, show empty or default to last 30 days
        $reportData = collect();
        if ($request->anyFilled(['start_date', 'end_date', 'category_id', 'status', 'type'])) {
            $reportData = $this->getReportData($startDate, $endDate, $categoryId, $status, $type);
        }

        return view('backoffice.reports.index', compact('categories', 'reportData', 'startDate', 'endDate', 'categoryId', 'status', 'type'));
    }

    public function downloadPdf(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $categoryId = $request->input('category_id');
        $status = $request->input('status');
        $type = $request->input('type');

        $reportData = $this->getReportData($startDate, $endDate, $categoryId, $status, $type);

        $pdf = Pdf::loadView('backoffice.reports.pdf', compact('reportData', 'startDate', 'endDate', 'status', 'type'));
        return $pdf->download('wkas-report-' . Carbon::now()->format('YmdHis') . '.pdf');
    }

    public function downloadXlsx(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $categoryId = $request->input('category_id');
        $status = $request->input('status');
        $type = $request->input('type');

        $reportData = $this->getReportData($startDate, $endDate, $categoryId, $status, $type);

        // Generate CSV file (fully compatible with Excel)
        $fileName = 'wkas-report-' . Carbon::now()->format('YmdHis') . '.csv';
        
        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $columns = ['Tanggal', 'Referensi No', 'Tipe', 'Kategori', 'Oleh', 'Deskripsi', 'Nominal', 'Status'];

        $callback = function() use($reportData, $columns) {
            $file = fopen('php://output', 'w');
            
            // Add UTF-8 BOM for Excel compatibility
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            fputcsv($file, $columns);

            foreach ($reportData as $row) {
                fputcsv($file, [
                    $row->date,
                    $row->reference_number,
                    $row->type,
                    $row->category_name,
                    $row->user_name,
                    $row->description,
                    $row->amount,
                    $row->status
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function getReportData($startDate, $endDate, $categoryId, $status, $type)
    {
        $deposits = collect();
        
        // Fetch deposits (always type=IN, Category=Iuran Wajib)
        // If type=OUT or category_id is set to something other than Iuran Wajib, deposits won't match
        $isIuranCategory = true;
        if ($categoryId) {
            $cat = Category::find($categoryId);
            if ($cat && strtolower($cat->name) !== 'iuran wajib') {
                $isIuranCategory = false;
            }
        }

        if ($type !== 'OUT' && $isIuranCategory) {
            $depositsQuery = Deposit::with('user');
            
            if ($startDate) {
                $depositsQuery->whereDate('created_at', '>=', $startDate);
            }
            if ($endDate) {
                $depositsQuery->whereDate('created_at', '<=', $endDate);
            }
            if ($status) {
                $depositsQuery->where('status', $status);
            }

            $deposits = $depositsQuery->get()->map(function($d) {
                return (object)[
                    'date' => $d->created_at->format('Y-m-d H:i'),
                    'raw_date' => $d->created_at,
                    'reference_number' => $d->reference_number,
                    'type' => 'IN',
                    'category_name' => 'Iuran Wajib',
                    'user_name' => $d->user->name ?? '-',
                    'description' => 'Iuran Bulanan - ' . $d->month . ($d->description ? ' (' . $d->description . ')' : ''),
                    'amount' => $d->amount,
                    'status' => $d->status
                ];
            });
        }

        // Fetch transactions
        $transactionsQuery = Transaction::with(['user', 'category']);
        
        if ($startDate) {
            $transactionsQuery->whereDate('created_at', '>=', $startDate);
        }
        if ($endDate) {
            $transactionsQuery->whereDate('created_at', '<=', $endDate);
        }
        if ($categoryId) {
            $transactionsQuery->where('category_id', $categoryId);
        }
        if ($status) {
            $transactionsQuery->where('status', $status);
        }
        if ($type) {
            $transactionsQuery->where('type', $type);
        }

        $transactions = $transactionsQuery->get()->map(function($t) {
            return (object)[
                'date' => $t->created_at->format('Y-m-d H:i'),
                'raw_date' => $t->created_at,
                'reference_number' => $t->reference_number,
                'type' => $t->type,
                'category_name' => $t->category->name ?? '-',
                'user_name' => $t->user->name ?? '-',
                'description' => $t->description,
                'amount' => $t->amount,
                'status' => $t->status
            ];
        });

        // Merge and sort
        return $deposits->concat($transactions)->sortByDesc('raw_date')->values();
    }
}
