<?php

namespace App\Http\Controllers;

use App\Models\Counter;
use App\Models\Payment;
use App\Models\QueueEntry;
use App\Models\User;
use App\Models\Visit;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class QueueController extends Controller
{

    public function allQueueDetails()
{
    // Integrate at least ONE procedure or VIEW into your system
    $queues = DB::table('patient_queue_details')
                ->orderBy('queue_created', 'desc')
                ->get();
    
    return view('queue.all', compact('queues'));
}

    public function call(Request $request, $id)
    {
        $queue = QueueEntry::findOrFail($id);
        $user = Auth::user();

        // Find staff's counter
        $counter = Counter::where('assigned_staff_id', $user->id)->first();

        if (! $counter) {
            return back()->with('error', 'You must be assigned to a counter to call patients.');
        }

        if ($counter->status === 'unavailable') {
            return back()->with('error', 'Please open your counter before calling patients.');
        }

        $queue->update([
            'status' => 'called',
            'called_at' => Carbon::now(),
        ]);

        $counter->update([
            'current_queue_id' => $queue->getKey(),
            'status' => 'busy',
        ]);

        return back()->with('success', "Called Queue #{$queue->queue_no}");
    }

    public function showPaymentForm(Request $request, $id)
    {
        $queue = QueueEntry::with(['patient', 'service', 'department'])->findOrFail($id);

        if ($queue->status !== 'called') {
            return back()->with('error', 'Queue must be in called status to process payment.');
        }

        $amount = (float) $queue->service->cost;

        return view('payment.form', compact('queue', 'amount'));
    }

    public function processPayment(Request $request, $id)
    {
        $validated = $request->validate([
            'payment_method' => 'required|in:cash,gcash,card',
            'amount' => 'required|numeric|min:0.01',
        ]);

        $queue = QueueEntry::with(['patient', 'service'])->findOrFail($id);
        $user = Auth::user();

        if ($queue->status !== 'called') {
            return back()->with('error', 'Invalid queue status for payment.');
        }

        // Create payment record
        $payment = Payment::create([
            'queue_id' => $queue->getKey(),
            'patient_id' => $queue->patient_id,
            'amount' => $validated['amount'],
            'payment_method' => $validated['payment_method'],
            'status' => 'completed',
            'paid_at' => Carbon::now(),
        ]);

        // Mark queue as paid (ready for doctor assignment)
        $queue->update([
            'status' => 'paid',
        ]);

        // Update counter status
        $counter = Counter::where('assigned_staff_id', $user->id)->first();
        if ($counter && $counter->current_queue_id == $queue->getKey()) {
            $counter->update([
                'current_queue_id' => null,
                'status' => $counter->status === 'unavailable' ? 'unavailable' : 'ready',
            ]);
        }

        // Redirect to receipt
        return redirect()->route('payment.receipt', $payment->id)->with('success', 'Payment processed successfully.');
    }

    public function paymentReceipt($paymentId)
    {
        $payment = Payment::with(['queue.patient', 'queue.service', 'queue.department'])->findOrFail($paymentId);

        return view('payment.receipt', compact('payment'));
    }

    public function exportReceiptPdf($paymentId)
    {
        $payment = Payment::with(['queue.patient', 'queue.service', 'queue.department'])->findOrFail($paymentId);

        $html = view('payment.receipt-pdf', compact('payment'))->render();

        $filename = 'receipt-'.$payment->id.'.pdf';
        file_put_contents(storage_path('app/'.$filename), $html);

        return response()->download(storage_path('app/'.$filename));
    }

    public function assignDoctor(Request $request, $id)
    {
        $validated = $request->validate([
            'doctor_id' => 'required|exists:users,id',
        ]);

        $queue = QueueEntry::findOrFail($id);
        $doctor = User::findOrFail($validated['doctor_id']);

        // Ensure doctor is in the same department and has doctor role
        if ($doctor->role->name !== 'Doctor' || $doctor->department_id !== $queue->department_id) {
            return back()->with('error', 'Invalid doctor assignment.');
        }

        // Create visit
        Visit::create([
            'visit_date' => Carbon::now(),
            'patient_id' => $queue->patient_id,
            'doctor_id' => $doctor->id,
            'queue_id' => $queue->id,
            'status' => 'ongoing',
        ]);

        // Update queue status
        $queue->update([
            'status' => 'assigned_to_doctor',
        ]);

        return back()->with('success', "Patient assigned to Dr. {$doctor->name}");
    }

    public function complete(Request $request, $id)
    {
        // Redirect to payment form instead of completing directly
        return redirect()->route('payment.form', $id);
    }

    public function cancel(Request $request, $id)
    {
        $queue = QueueEntry::findOrFail($id);
        $user = Auth::user();
        $counter = Counter::where('assigned_staff_id', $user->id)->first();

        $queue->update([
            'status' => 'cancelled',
        ]);

        if ($counter && $counter->current_queue_id == $queue->getKey()) {
            $counter->update([
                'current_queue_id' => null,
                'status' => 'ready',
            ]);
        }

        return back()->with('success', "Cancelled Queue #{$queue->queue_no}");
    }

    public function toggleCounter(Request $request)
    {
        $user = Auth::user();
        $counter = Counter::where('assigned_staff_id', $user->id)->first();

        if (! $counter) {
            return back()->with('error', 'No counter assigned to you.');
        }

        $newStatus = $counter->status == 'unavailable' ? 'ready' : 'unavailable';

        // If busy, it stays busy even if technically "unavailable" for next patients
        if ($counter->current_queue_id && $newStatus == 'ready') {
            $newStatus = 'busy';
        }

        $counter->update(['status' => $newStatus]);

        return back()->with('success', "Counter status updated to {$newStatus}.");
    }
}
