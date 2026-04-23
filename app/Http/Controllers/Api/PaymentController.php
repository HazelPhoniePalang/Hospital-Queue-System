<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Counter;
use App\Models\Payment;
use App\Models\QueueEntry;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PaymentController extends Controller
{
    public function processCashPayment(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'queue_id' => 'required|exists:queue_entries,id',
            'amount' => 'required|numeric|min:0.01',
        ]);

        return $this->processPayment($validated, 'cash');
    }

    public function processGcashPayment(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'queue_id' => 'required|exists:queue_entries,id',
            'amount' => 'required|numeric|min:0.01',
            'gcash_ref' => 'required|string|max:255',
        ]);

        return $this->processPayment($validated, 'gcash');
    }

    public function processCardPayment(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'queue_id' => 'required|exists:queue_entries,id',
            'amount' => 'required|numeric|min:0.01',
            'card_last_four' => 'required|string|size:4',
            'card_brand' => 'required|string|in:visa,mastercard,amex',
        ]);

        return $this->processPayment($validated, 'card');
    }

    private function processPayment(array $data, string $method): JsonResponse
    {
        $queue = QueueEntry::with(['patient', 'service'])->findOrFail($data['queue_id']);

        if ($queue->status !== 'called') {
            return response()->json([
                'success' => false,
                'message' => 'Invalid queue status for payment. Queue must be in called status.',
            ], 400);
        }

        $payment = Payment::create([
            'queue_id' => $queue->getKey(),
            'patient_id' => $queue->patient_id,
            'amount' => $data['amount'],
            'payment_method' => $method,
            'status' => 'completed',
            'paid_at' => Carbon::now(),
        ]);

        $queue->update([
            'status' => 'paid',
        ]);

        if (Auth::check()) {
            $user = Auth::user();
            $counter = Counter::where('assigned_staff_id', $user->id)->first();
            if ($counter && $counter->current_queue_id == $queue->getKey()) {
                $counter->update([
                    'current_queue_id' => null,
                    'status' => $counter->status === 'unavailable' ? 'unavailable' : 'ready',
                ]);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Payment processed successfully.',
            'data' => [
                'payment_id' => $payment->id,
                'queue_id' => $queue->id,
                'queue_no' => $queue->queue_no,
                'amount' => (float) $payment->amount,
                'payment_method' => $payment->payment_method,
                'status' => $payment->status,
                'paid_at' => $payment->paid_at->toIso8601String(),
            ],
        ]);
    }

    public function show(int $paymentId): JsonResponse
    {
        $payment = Payment::with(['queue.patient', 'queue.service', 'queue.department'])->findOrFail($paymentId);

        return response()->json([
            'success' => true,
            'data' => [
                'payment_id' => $payment->id,
                'queue_id' => $payment->queue_id,
                'queue_no' => $payment->queue->queue_no,
                'patient' => [
                    'name' => $payment->queue->patient->first_name.' '.$payment->queue->patient->last_name,
                    'contact' => $payment->queue->patient->contact_no,
                ],
                'service' => $payment->queue->service->service_name,
                'department' => $payment->queue->department->name,
                'amount' => (float) $payment->amount,
                'payment_method' => $payment->payment_method,
                'status' => $payment->status,
                'paid_at' => $payment->paid_at?->toIso8601String(),
            ],
        ]);
    }

    public function receipt(int $paymentId): JsonResponse
    {
        $payment = Payment::with(['queue.patient', 'queue.service', 'queue.department'])->findOrFail($paymentId);

        return response()->json([
            'success' => true,
            'data' => [
                'receipt_id' => str_pad($payment->id, 6, '0', STR_PAD_LEFT),
                'payment_id' => $payment->id,
                'patient_name' => $payment->queue->patient->first_name.' '.$payment->queue->patient->last_name,
                'contact_no' => $payment->queue->patient->contact_no,
                'queue_no' => $payment->queue->queue_no,
                'department' => $payment->queue->department->name,
                'service' => $payment->queue->service->service_name,
                'amount' => number_format($payment->amount, 2),
                'payment_method' => strtoupper($payment->payment_method),
                'paid_at' => $payment->paid_at?->format('M d, Y H:i A'),
            ],
        ]);
    }
}
