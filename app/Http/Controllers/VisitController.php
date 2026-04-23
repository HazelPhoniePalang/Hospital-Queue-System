<?php

namespace App\Http\Controllers;

use App\Models\QueueEntry;
use App\Models\Visit;
use Carbon\Carbon;
use Illuminate\Http\Request;

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
        $queue = QueueEntry::findOrFail($queue_id);

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

        $patient = $queue->patient;
        $department = $queue->department;
        $service = $queue->service;

        $pdfContent = view('visits.medical-certificate', compact('visit', 'queue', 'patient', 'department', 'service'))->render();

        $filename = 'medical-certificate-'.$visit->id.'.pdf';
        file_put_contents(storage_path('app/'.$filename), $pdfContent);

        return redirect()->route('dashboard')->with('success', 'Visit record saved successfully.')->with('download_pdf', $filename);
    }

    public function downloadPdf($filename)
    {
        $path = storage_path('app/'.$filename);

        if (! file_exists($path)) {
            return redirect()->route('dashboard')->with('error', 'PDF not found.');
        }

        return response()->download($path);
    }
}
