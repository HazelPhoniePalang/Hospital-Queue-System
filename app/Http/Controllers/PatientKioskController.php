<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Patient;
use App\Models\QueueCounter;
use App\Models\QueueEntry;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class PatientKioskController extends Controller
{
    public function index()
    {
        return view('kiosk.index');
    }

    public function showForm()
    {
        $departments = Cache::remember('departments_with_services', 3600, function () {
            return Department::with('services')->get();
        });

        return view('kiosk.form', compact('departments'));
    }

    public function store(Request $request)
    {
        // Change 'Symptoms' to 'symptoms' in validation
        $validated = $request->validate([
            'first_name'    => 'required|string|max:255',
            'last_name'     => 'required|string|max:255',
            'birth_date'    => 'required|date',
            'gender'        => 'required|string|max:30',
            'contact_no'    => 'required|string|max:40',
            'address'       => 'nullable|string',
            'service_id'    => 'required|exists:services,id',
            'department_id' => 'required|exists:departments,id',
            'symptoms'      => 'nullable|string',  // ✅ lowercase
        ]);

        return DB::transaction(function () use ($validated) {
            // Check if patient already exists by contact number and name
            $patient = Patient::where('contact_no', $validated['contact_no'])
                ->where('first_name', $validated['first_name'])
                ->where('last_name', $validated['last_name'])
                ->first();

            if (! $patient) {
                $patient = Patient::create([
                    'first_name' => $validated['first_name'],
                    'last_name' => $validated['last_name'],
                    'birth_date' => $validated['birth_date'],
                    'gender' => $validated['gender'],
                    'contact_no' => $validated['contact_no'],
                    'address' => $validated['address'],
                    'symptoms' => $validated['symptoms'] ?? null,
                ]);
            }

            // Generate Queue Number (e.g., GMED-001)
            $dept = Department::find($validated['department_id']);
            $today = Carbon::today()->toDateString();

            // Use firstOrCreate to avoid race conditions when creating the counter
            $counter = QueueCounter::firstOrCreate(
                ['department_id' => $dept->id, 'counter_date' => $today],
                ['current_count' => 0]
            );

            // Lock the record for the current transaction
            $counter = QueueCounter::where('id', $counter->id)->lockForUpdate()->first();

            // Increment and get the next number
            $counter->increment('current_count');
            $nextNumber = $counter->current_count;

            $queueNo = sprintf('%s-%03d', $dept->code, $nextNumber);

            $queue = QueueEntry::create([
                'queue_no'       => $queueNo,
                'priority_level' => 1,
                'status'         => 'waiting',
                'patient_id'     => $patient->id,
                'department_id'  => $dept->id,
                'service_id'     => $validated['service_id'],
                'symptoms'       => $validated['symptoms'] ?? null,  // ✅ lowercase
            ]);

            return redirect()->route('kiosk.success', $queue->id);
        });
    }

    public function success($id)
    {
        $queue = QueueEntry::with(['patient', 'department', 'service'])->findOrFail($id);

        return view('kiosk.success', compact('queue'));
    }
}
