<?php

namespace App\Http\Controllers;

use App\Models\Obituary;
use App\Models\Payment;
use App\Services\MpesaService;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    protected MpesaService $mpesaService;

    public function __construct(MpesaService $mpesaService)
    {
        $this->mpesaService = $mpesaService;
    }

    public function checkout(Obituary $obituary)
    {
        if ($obituary->status === 'published' || $obituary->status === 'pending_verification') {
            return redirect()->route('payments.success', $obituary->id);
        }

        $latestPayment = $obituary->latestPayment;

        return view('payments.checkout', compact('obituary', 'latestPayment'));
    }

    public function initiateStkPush(Request $request, Obituary $obituary)
    {
        $request->validate([
            'phone_number' => ['required', 'string', 'min:9', 'max:20'],
        ]);

        $phoneNumber = $request->input('phone_number');
        $cost = (float) \App\Models\Setting::get('obituary_publishing_cost', 500.00);
        $result = $this->mpesaService->initiateStkPush($obituary, $phoneNumber, $cost);

        if (!$result['success']) {
            return back()->with('error', $result['message']);
        }

        // If mock mode is enabled, auto-complete for smooth developer test experience
        if (!empty($result['is_mock'])) {
            $payment = $result['payment'];
            $this->mpesaService->simulateMockCompletion($payment);

            return redirect()->route('payments.success', $obituary->id)
                ->with('success', 'M-Pesa payment simulated successfully! Your obituary is now pending verification.');
        }

        return redirect()->route('payments.checkout', $obituary->id)
            ->with('success', 'M-Pesa STK Push prompt sent to ' . $phoneNumber . '. Please enter your M-Pesa PIN on your phone to complete payment.');
    }

    public function checkStatus(Obituary $obituary)
    {
        $payment = $obituary->latestPayment;

        if ($payment && $payment->status === 'pending' && $payment->checkout_request_id) {
            $this->mpesaService->queryStkStatus($payment);
            $payment->refresh();
            $obituary->refresh();
        }

        $isCompleted = in_array($obituary->status, ['pending_verification', 'published']) || ($payment && $payment->status === 'completed');

        return response()->json([
            'status' => $payment ? $payment->status : 'pending',
            'obituary_status' => $obituary->status,
            'receipt' => $payment ? $payment->mpesa_receipt_number : null,
            'is_completed' => $isCompleted,
        ]);
    }

    public function success(Obituary $obituary)
    {
        $payment = $obituary->latestPayment;

        return view('payments.success', compact('obituary', 'payment'));
    }

    public function handleCallback(Request $request)
    {
        $payload = $request->all();
        $processed = $this->mpesaService->processCallback($payload);

        return response()->json([
            'ResultCode' => $processed ? 0 : 1,
            'ResultDesc' => $processed ? 'Accepted' : 'Rejected',
        ]);
    }
}
