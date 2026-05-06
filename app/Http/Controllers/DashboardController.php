<?php

namespace App\Http\Controllers;

use App\Models\Counter;
use App\Models\Department;
use App\Models\Patient;
use App\Models\QueueEntry;
use App\Models\Service;
use App\Models\User;
use App\Models\Visit;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if (in_array($user->role->name, ['Administrator', 'Admin'], true)) {
            return $this->adminDashboard();
        } elseif (in_array($user->role->name, ['Hospital Staff', 'Staff'], true)) {
            return $this->staffDashboard();
        } elseif ($user->role->name === 'Doctor') {
            return $this->doctorDashboard();
        }

        return redirect('/');
    }

    public function staff()
    {
        return $this->staffDashboard();
    }

    public function patient()
    {
        // Redirect to kiosk or patient dashboard
        return redirect()->route('kiosk.index');
    }

    private function adminDashboard()
    {
        $stats = [
            'total_users' => User::count(),
            'total_departments' => Department::count(),
            'total_services' => Service::count(),
            'total_queues_today' => QueueEntry::whereDate('created_at', Carbon::today())->count(),
        ];

        $departments = Department::withCount('queues')->get();

        return view('dashboard.admin', compact('stats', 'departments'));
    }

    private function staffDashboard()
    {
        $user = Auth::user();
        $department = $user->department;

        // Staff can handle all waiting/called patients regardless of department.
        $activeQueues = QueueEntry::with(['department', 'patient', 'service'])
            ->whereDate('created_at', Carbon::today())
            ->whereIn('status', ['waiting', 'called'])
            ->orderBy('created_at', 'asc')
            ->get();

        // Paid queues ready for doctor assignment - load department with users and their roles
        $paidQueues = QueueEntry::with(['department.users.role', 'department.users', 'patient', 'service'])
            ->whereDate('created_at', Carbon::today())
            ->where('status', 'paid')
            ->orderBy('updated_at', 'asc')
            ->get();

        $allActiveQueues = $activeQueues;
        $counter = Counter::where('assigned_staff_id', $user->id)->first();

        $stats = [
            'waiting' => $activeQueues->where('status', 'waiting')->count(),
            'called' => $activeQueues->where('status', 'called')->count(),
            'served' => QueueEntry::whereDate('created_at', Carbon::today())
                ->where('status', 'completed')
                ->count(),
        ];

        return view('dashboard.staff', compact('activeQueues', 'paidQueues', 'allActiveQueues', 'stats', 'counter', 'department'));
    }

    private function doctorDashboard()
    {
        $user = Auth::user();
        $department = $user->department;

        if (! $department) {
            return view('dashboard.error', ['message' => 'Doctor not assigned to a department.']);
        }

        $assignedVisits = Visit::with(['patient', 'queue.service', 'queue.department'])
            ->where('doctor_id', $user->id)
            ->whereIn('status', ['ongoing', 'assigned'])
            ->orderBy('visit_date', 'asc')
            ->get();

       $stats = [
            'today_patients' => Visit::where('doctor_id', $user->id)
                ->where('status', 'completed')
                ->whereDate('visit_date', Carbon::today())
                ->count(),

            'week_patients' => Visit::where('doctor_id', $user->id)
                ->where('status', 'completed')
                ->whereBetween('visit_date', [
                    Carbon::now()->startOfWeek(),
                    Carbon::now()->endOfWeek()
                ])
                ->count(),
        ];

        return view('dashboard.doctor', compact('assignedVisits', 'stats', 'department'));
    }

    public function patientRecords(Request $request)
    {
        $user = auth()->user();

        // Only admin, doctor, and staff can view patient records
        if (! in_array($user->role->name, ['Administrator', 'Doctor', 'Hospital Staff', 'Staff'])) {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access.');
        }

        $query = Patient::with(['user', 'queues.department', 'queues.service', 'visits.doctor', 'visits.queue']);

        // Search by name or contact
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('contact_no', 'like', "%{$search}%");
            });
        }

        // Filter by department (for doctors and staff)
        if ($request->has('department_id') && $request->department_id) {
            $query->whereHas('queues', function ($q) use ($request) {
                $q->where('department_id', $request->department_id);
            });
        }

        $patients = $query->orderBy('created_at', 'desc')->paginate(20);
        $departments = Department::all();

        return view('dashboard.patient-records', compact('patients', 'departments'));
    }
}
