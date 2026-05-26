<?php

namespace App\Http\Controllers\Backoffice;

use App\Http\Controllers\Controller;
use App\Models\PaymentPlan;
use Illuminate\Http\Request;

class PaymentPlanController extends Controller
{
    public function index()
    {
        $paymentPlans = PaymentPlan::with(['category', 'user'])->latest()->get();
        return view('backoffice.payment-plans.index', compact('paymentPlans'));
    }

    public function show(PaymentPlan $paymentPlan)
    {
        $paymentPlan->load(['category', 'user', 'transaction']);
        return response()->json($paymentPlan);
    }
}
