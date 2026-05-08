<?php

namespace App\Http\Controllers;

use App\Models\QueueEntry;
use App\Models\Visit;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class VisitController extends Controller
{
    public function show($queue_id)
    {
        $queue = QueueEntry::with(['patient', 'department', 'service'])->findOrFail($queue_id);

        $user = auth()->user();
        if ($user && $user->role && $user->role->name === 'Doctor') {
            $visit = Visit::where('queue_id', $queue_id)->where('doctor_id', $user->id)->first();
            if (! $visit) {
                return redirect()->route('dashboard')->with('error', 'You are not authorized to consult this patient.');
            }
        }

        $pastVisits = Visit::where('patient_id', $queue->patient_id)
            ->where('id', '!=', $queue->visit?->id)
            ->orderBy('visit_date', 'desc')
            ->get();

        return view('visits.consultation', compact('queue', 'pastVisits'));
    }

    public function store(Request $request, $queue_id)
    {
        $queue = QueueEntry::with(['patient', 'department', 'service'])->findOrFail($queue_id);

        $validated = $request->validate([
            'notes' => 'required|string',
            'diagnosis' => 'required|string',
            'status' => 'required|string|max:30',
        ]);

        $visit = Visit::updateOrCreate(
            ['queue_id' => $queue->id],
            [
                'visit_date' => Carbon::now(),
                'notes' => $validated['notes'],
                'diagnosis' => $validated['diagnosis'],
                'status' => $validated['status'],
                'patient_id' => $queue->patient_id,
                'doctor_id' => auth()->id(),
            ]
        );

        // Generate and save the medical certificate PDF after consultation.
        $patient = $queue->patient;
        $department = $queue->department;
        $service = $queue->service;

        $pdf = Pdf::loadView('visits.medical-certificate', compact('visit', 'queue', 'patient', 'department', 'service'));

        $filename = 'medical-certificate-'.$visit->id.'.pdf';
        $relativePath = 'medical-certificates/'.$filename;

        Storage::disk('local')->put($relativePath, $pdf->output());

        return redirect()->route('dashboard')
            ->with('success', 'Consultation saved successfully. The medical certificate PDF was automatically saved.')
            ->with('download_pdf', $filename)
            ->with('medical_certificate_path', $relativePath)
            ->with('visit_id', $visit->id);
    }

    public function downloadPdf($filename)
    {
        $path = Storage::disk('local')->path('medical-certificates/'.$filename);

        if (! file_exists($path)) {
            $path = storage_path('app/'.$filename);
        }

        if (! file_exists($path)) {
            return redirect()->route('dashboard')->with('error', 'PDF not found.');
        }

        return response()->download($path);
    }

    /**
     * Download Clinical Notes & Diagnosis PDF for patient copy
     */
    public function downloadClinicalNotes($visitId)
    {
        $visit = Visit::with(['queue.patient', 'queue.department', 'queue.service', 'doctor'])->findOrFail($visitId);

        // Ensure the logged-in user is authorized (doctor who created visit or admin/staff)
        $user = auth()->user();
        if ($user->role->name === 'Doctor' && $visit->doctor_id !== $user->id) {
            return redirect()->route('dashboard')->with('error', 'You are not authorized to access this record.');
        }

        $pdf = Pdf::loadView('visits.clinical-notes-pdf', compact('visit'));

        $filename = 'clinical-notes-'.$visit->id.'.pdf';

        return $pdf->download($filename);
    }
}
