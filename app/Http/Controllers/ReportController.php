<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Payment;
use App\Models\QueueEntry;
use App\Models\Visit;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $type = $request->get('type', 'queue');
        $departmentId = $request->get('department');
        $status = $request->get('status');
        $startDate = $request->get('start_date', now()->toDateString());
        $endDate = $request->get('end_date', now()->toDateString());

        $departments = Department::all();

        $queues = match ($type) {
            'queue' => QueueEntry::with(['patient', 'department', 'service'])
                ->whereBetween('created_at', [$startDate.' 00:00:00', $endDate.' 23:59:59'])
                ->when($departmentId, fn ($q) => $q->where('department_id', $departmentId))
                ->when($status, fn ($q) => $q->where('status', $status))
                ->orderBy('created_at', 'desc')
                ->get(),
            'payment' => Payment::with(['patient', 'queue.department'])
                ->whereBetween('created_at', [$startDate.' 00:00:00', $endDate.' 23:59:59'])
                ->when($departmentId, fn ($q) => $q->whereHas('queue', fn ($sq) => $sq->where('department_id', $departmentId)))
                ->when($status, fn ($q) => $q->where('status', $status))
                ->orderBy('created_at', 'desc')
                ->get(),
            'visit' => Visit::with(['queue.patient', 'queue.department'])
                ->whereBetween('created_at', [$startDate.' 00:00:00', $endDate.' 23:59:59'])
                ->when($departmentId, fn ($q) => $q->whereHas('queue', fn ($sq) => $q->where('department_id', $departmentId)))
                ->orderBy('created_at', 'desc')
                ->get(),
            default => collect(),
        };

        return view('admin.reports', compact('queues', 'departments', 'type', 'departmentId', 'status', 'startDate', 'endDate'));
    }

    public function export(Request $request)
    {
        $type = $request->get('type', 'queue');
        $departmentId = $request->get('department');
        $status = $request->get('status');
        $startDate = $request->get('start_date', now()->toDateString());
        $endDate = $request->get('end_date', now()->toDateString());
        $format = $request->get('format', 'csv');

        $fileName = $type.'_report_'.date('Y-m-d', strtotime($startDate)).'_to_'.date('Y-m-d', strtotime($endDate));

        if ($type === 'queue') {
            return $this->exportQueueReport($request, $format, $fileName);
        } elseif ($type === 'payment') {
            return $this->exportPaymentReport($request, $format, $fileName);
        } elseif ($type === 'visit') {
            return $this->exportVisitReport($request, $format, $fileName);
        }

        return back()->with('error', 'Invalid report type');
    }

    private function exportQueueReport(Request $request, string $format, string $fileName): StreamedResponse
    {
        $departmentId = $request->get('department');
        $status = $request->get('status');
        $startDate = $request->get('start_date', now()->toDateString());
        $endDate = $request->get('end_date', now()->toDateString());

        $query = QueueEntry::with(['patient', 'department', 'service'])
            ->whereBetween('created_at', [$startDate.' 00:00:00', $endDate.' 23:59:59']);

        if ($departmentId) {
            $query->where('department_id', $departmentId);
        }

        if ($status) {
            $query->where('status', $status);
        }

        $queues = $query->orderBy('created_at', 'desc')->get();

        if ($format === 'pdf') {
            return response()->streamDownload(function () use ($queues) {
                echo view('admin.exports.queue-pdf', compact('queues'))->render();
            }, $fileName.'.pdf', ['Content-Type' => 'application/pdf']);
        }

        $headers = ['Queue No', 'Patient Name', 'Department', 'Service', 'Status', 'Created At', 'Called At', 'Completed At'];
        $rows = $queues->map(function ($q) {
            $patientName = $q->patient ? $q->patient->name : 'N/A';

            return [
                $q->queue_no,
                $patientName,
                $q->department ? $q->department->name : 'N/A',
                $q->service ? $q->service->name : 'N/A',
                $q->status,
                $q->created_at->format('Y-m-d H:i:s'),
                $q->called_at ? $q->called_at->format('Y-m-d H:i:s') : 'N/A',
                $q->completed_at ? $q->completed_at->format('Y-m-d H:i:s') : 'N/A',
            ];
        });

        return $this->downloadCsv($headers, $rows, $fileName);
    }

    private function exportPaymentReport(Request $request, string $format, string $fileName): StreamedResponse
    {
        $departmentId = $request->get('department');
        $status = $request->get('status');
        $startDate = $request->get('start_date', now()->toDateString());
        $endDate = $request->get('end_date', now()->toDateString());

        $query = Payment::with(['patient', 'queue.department'])
            ->whereBetween('created_at', [$startDate.' 00:00:00', $endDate.' 23:59:59']);

        if ($departmentId) {
            $query->whereHas('queue', function ($q) use ($departmentId) {
                $q->where('department_id', $departmentId);
            });
        }

        if ($status) {
            $query->where('status', $status);
        }

        $payments = $query->orderBy('created_at', 'desc')->get();

        if ($format === 'pdf') {
            return response()->streamDownload(function () use ($payments) {
                echo view('admin.exports.payment-pdf', compact('payments'))->render();
            }, $fileName.'.pdf', ['Content-Type' => 'application/pdf']);
        }

        $headers = ['Payment ID', 'Patient Name', 'Department', 'Amount', 'Payment Method', 'Status', 'Paid At'];
        $rows = $payments->map(function ($p) {
            return [
                $p->getKey(),
                $p->patient ? $p->patient->name : 'N/A',
                $p->queue && $p->queue->department ? $p->queue->department->name : 'N/A',
                $p->amount,
                $p->payment_method,
                $p->status,
                $p->paid_at ? $p->paid_at->format('Y-m-d H:i:s') : 'N/A',
            ];
        });

        return $this->downloadCsv($headers, $rows, $fileName);
    }

    private function exportVisitReport(Request $request, string $format, string $fileName): StreamedResponse
    {
        $departmentId = $request->get('department');
        $status = $request->get('status');
        $startDate = $request->get('start_date', now()->toDateString());
        $endDate = $request->get('end_date', now()->toDateString());

        $query = Visit::with(['queue.patient', 'queue.department'])
            ->whereBetween('created_at', [$startDate.' 00:00:00', $endDate.' 23:59:59']);

        if ($departmentId) {
            $query->whereHas('queue', function ($q) use ($departmentId) {
                $q->where('department_id', $departmentId);
            });
        }

        $visits = $query->orderBy('created_at', 'desc')->get();

        if ($format === 'pdf') {
            return response()->streamDownload(function () use ($visits) {
                echo view('admin.exports.visit-pdf', compact('visits'))->render();
            }, $fileName.'.pdf', ['Content-Type' => 'application/pdf']);
        }

        $headers = ['Visit ID', 'Patient Name', 'Department', 'Doctor Notes', 'Diagnosis', 'Created At'];
        $rows = $visits->map(function ($v) {
            return [
                $v->getKey(),
                $v->queue && $v->queue->patient ? $v->queue->patient->name : 'N/A',
                $v->queue && $v->queue->department ? $v->queue->department->name : 'N/A',
                $v->doctor_notes,
                $v->diagnosis,
                $v->created_at->format('Y-m-d H:i:s'),
            ];
        });

        return $this->downloadCsv($headers, $rows, $fileName);
    }

    private function downloadCsv(array $headers, $rows, string $fileName): StreamedResponse
    {
        $callback = function () use ($headers, $rows) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, $headers);
            foreach ($rows as $row) {
                fputcsv($handle, $row);
            }
            fclose($handle);
        };

        return response()->stream($callback, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="'.$fileName.'.csv"',
        ]);
    }
}
