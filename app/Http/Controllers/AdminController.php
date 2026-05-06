<?php

namespace App\Http\Controllers;

use App\Models\Counter;
use App\Models\Department;
use App\Models\Patient;
use App\Models\Role;
use App\Models\Service;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    // Patient Management
    public function patients()
    {
        $patients = Patient::withCount(['queues', 'visits'])
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->get();

        return view('admin.patients', compact('patients'));
    }

    public function storePatient(Request $request)
    {
        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'birth_date' => ['required', 'date', 'before_or_equal:today'],
            'gender' => ['required', 'string', 'max:30'],
            'contact_no' => ['nullable', 'string', 'max:40'],
            'address' => ['nullable', 'string', 'max:1000'],
        ]);

        Patient::create($validated);

        return back()->with('success', 'Patient created successfully.');
    }

    public function updatePatient(Request $request, $id)
    {
        $patient = Patient::findOrFail($id);

        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'birth_date' => ['required', 'date', 'before_or_equal:today'],
            'gender' => ['required', 'string', 'max:30'],
            'contact_no' => ['nullable', 'string', 'max:40'],
            'address' => ['nullable', 'string', 'max:1000'],
        ]);

        $patient->update($validated);

        return back()->with('success', 'Patient updated successfully.');
    }

    public function deletePatient($id)
    {
        Patient::findOrFail($id)->delete();

        return back()->with('success', 'Patient archived successfully.');
    }

    public function archivedPatients()
    {
        $items = Patient::onlyTrashed()
            ->orderByDesc('deleted_at')
            ->get();

        return view('admin.archive', [
            'title' => 'Archived patients',
            'eyebrow' => 'Patient Archive',
            'description' => 'Patient records moved out of the active patient list.',
            'backRoute' => route('admin.patients'),
            'restoreRouteName' => 'admin.patients.restore',
            'items' => $items,
            'columns' => ['Patient', 'Birth Date', 'Contact'],
            'type' => 'patients',
        ]);
    }

    public function restorePatient($id)
    {
        Patient::onlyTrashed()->findOrFail($id)->restore();

        return back()->with('success', 'Patient restored successfully.');
    }

    // User Management
    public function users()
    {
        $users = User::with(['role', 'department', 'assignedCounters'])->get();
        $roles = Role::all();
        $departments = Department::all();
        $counters = Counter::with(['department'])->get();

        return view('admin.users', compact('users', 'roles', 'departments', 'counters'));
    }

    public function storeUser(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
            'role_id' => 'required|exists:roles,id',
            'department_id' => 'nullable|exists:departments,id',
            'counter_id' => 'nullable|exists:counters,id',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role_id' => $validated['role_id'],
            'department_id' => $validated['department_id'],
        ]);

        // Assign counter if provided
        if (! empty($validated['counter_id'])) {
            $counter = Counter::find($validated['counter_id']);
            if ($counter) {
                // Unassign from any existing staff
                Counter::where('assigned_staff_id', $user->id)->update(['assigned_staff_id' => null]);
                // Assign to new staff
                $counter->update(['assigned_staff_id' => $user->id]);
            }
        }

        return back()->with('success', 'User created successfully.');
    }

    public function updateUser(Request $request, $id)
    {
        // Debug logging
        \Log::info('Update user request received', [
            'id' => $id,
            'method' => $request->method(),
            'all_data' => $request->all(),
            'route' => $request->route() ? $request->route()->getName() : 'unknown',
        ]);

        $user = User::findOrFail($id);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email,'.$id],
            'password' => 'nullable|min:6',
            'role_id' => 'required|exists:roles,id',
            'department_id' => 'nullable|exists:departments,id',
            'counter_id' => 'nullable|exists:counters,id',
        ]);

        $updateData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role_id' => $validated['role_id'],
            'department_id' => $validated['department_id'],
        ];

        // Only update password if provided
        if (! empty($validated['password'])) {
            $updateData['password'] = Hash::make($validated['password']);
        }

        try {
            $user->update($updateData);

            // Handle counter assignment
            // First, unassign this user from any counters they're currently assigned to
            Counter::where('assigned_staff_id', $user->id)->update(['assigned_staff_id' => null]);

            // Then assign the new counter if provided
            if (! empty($validated['counter_id'])) {
                $counter = Counter::find($validated['counter_id']);
                if ($counter) {
                    // Unassign from any existing staff
                    $counter->update(['assigned_staff_id' => null]);
                    // Assign to this user
                    $counter->update(['assigned_staff_id' => $user->id]);
                }
            }

            \Log::info('User updated successfully', ['user_id' => $id, 'data' => $updateData]);

            return back()->with('success', 'User updated successfully.');
        } catch (\Exception $e) {
            \Log::error('Failed to update user', ['error' => $e->getMessage(), 'user_id' => $id]);

            return back()->with('error', 'Failed to update user. Please try again.');
        }

        try {
            $user->update($updateData);
            \Log::info('User updated successfully', ['user_id' => $id, 'data' => $updateData]);

            return back()->with('success', 'User updated successfully.');
        } catch (\Exception $e) {
            \Log::error('Failed to update user', ['error' => $e->getMessage(), 'user_id' => $id]);

            return back()->with('error', 'Failed to update user. Please try again.');
        }
    }

    public function deleteUser($id)
    {
        $user = User::findOrFail($id);
        Counter::where('assigned_staff_id', $user->getKey())->update(['assigned_staff_id' => null]);
        $user->delete();

        return back()->with('success', 'User archived successfully.');
    }

    public function archivedUsers()
    {
        $items = User::onlyTrashed()
            ->with(['role', 'department'])
            ->orderByDesc('deleted_at')
            ->get();

        return view('admin.archive', [
            'title' => 'Archived users',
            'eyebrow' => 'User Archive',
            'description' => 'User accounts moved out of active staff, doctor, and admin lists.',
            'backRoute' => route('admin.users'),
            'restoreRouteName' => 'admin.users.restore',
            'items' => $items,
            'columns' => ['Name', 'Email', 'Role'],
            'type' => 'users',
        ]);
    }

    public function restoreUser($id)
    {
        User::onlyTrashed()->findOrFail($id)->restore();

        return back()->with('success', 'User restored successfully.');
    }

    // Department Management
    public function departments()
    {
        $departments = Department::withCount('services')->get();

        return view('admin.departments', compact('departments'));
    }

    public function storeDepartment(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|unique:departments',
            'code' => 'required|string|unique:departments|max:10',
            'location' => 'nullable|string',
        ]);

        Department::create($validated);

        return back()->with('success', 'Department created successfully.');
    }

    public function updateDepartment(Request $request, $id)
    {
        $department = Department::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|unique:departments,name,'.$id.',id',
            'code' => 'required|string|unique:departments,code,'.$id.',id|max:10',
            'location' => 'nullable|string',
        ]);

        $department->update($validated);

        return back()->with('success', 'Department updated successfully.');
    }

    public function deleteDepartment($id)
    {
        Department::findOrFail($id)->delete();

        return back()->with('success', 'Department archived successfully.');
    }

    public function archivedDepartments()
    {
        $items = Department::onlyTrashed()
            ->withCount('services')
            ->orderByDesc('deleted_at')
            ->get();

        return view('admin.archive', [
            'title' => 'Archived departments',
            'eyebrow' => 'Department Archive',
            'description' => 'Departments moved out of active registration and routing lists.',
            'backRoute' => route('admin.departments'),
            'restoreRouteName' => 'admin.departments.restore',
            'items' => $items,
            'columns' => ['Department', 'Code', 'Location'],
            'type' => 'departments',
        ]);
    }

    public function restoreDepartment($id)
    {
        Department::onlyTrashed()->findOrFail($id)->restore();

        return back()->with('success', 'Department restored successfully.');
    }

    // Service Management
    public function services()
    {
        $services = Service::with('department')->get();
        $departments = Department::all();

        return view('admin.services', compact('services', 'departments'));
    }

    public function storeService(Request $request)
    {
        $validated = $request->validate([
            'department_id' => 'required|exists:departments,id',
            'service_name' => 'required|string',
            'average_duration' => 'required|integer',
            'cost' => 'required|numeric',
        ]);

        Service::create($validated);

        return back()->with('success', 'Service created successfully.');
    }

    public function updateService(Request $request, $id)
    {
        $service = Service::findOrFail($id);

        $validated = $request->validate([
            'department_id' => 'required|exists:departments,id',
            'service_name' => 'required|string',
            'average_duration' => 'required|integer',
            'cost' => 'required|numeric',
        ]);

        $service->update($validated);

        return back()->with('success', 'Service updated successfully.');
    }

    public function deleteService($id)
    {
        Service::findOrFail($id)->delete();

        return back()->with('success', 'Service archived successfully.');
    }

    public function archivedServices()
    {
        $items = Service::onlyTrashed()
            ->with(['department'])
            ->orderByDesc('deleted_at')
            ->get();

        return view('admin.archive', [
            'title' => 'Archived services',
            'eyebrow' => 'Service Archive',
            'description' => 'Services and prices moved out of active queue registration.',
            'backRoute' => route('admin.services'),
            'restoreRouteName' => 'admin.services.restore',
            'items' => $items,
            'columns' => ['Service', 'Department', 'Price'],
            'type' => 'services',
        ]);
    }

    public function restoreService($id)
    {
        Service::onlyTrashed()->findOrFail($id)->restore();

        return back()->with('success', 'Service restored successfully.');
    }

    // Counter Management
    public function counters(Request $request)
    {
        $departments = Department::all();

        $counters = Counter::with(['department', 'assignedStaff'])->get();

        $staffQuery = User::query();

        if ($request->has('department_id') && $request->department_id) {
            $staffQuery->where('department_id', $request->department_id);
        }

        $staff = $staffQuery->whereIn('role_id', [2, 3, 4, 5])->with(['role', 'department'])->get();

        return view('admin.counters', compact('counters', 'departments', 'staff'));
    }

    public function storeCounter(Request $request)
    {
        $validated = $request->validate([
            'department_id' => 'required|exists:departments,id',
            'name' => 'required|string|max:255',
            'assigned_staff_id' => 'nullable|exists:users,id',
        ]);

        Counter::create([
            'department_id' => $validated['department_id'],
            'name' => $validated['name'],
            'assigned_staff_id' => $validated['assigned_staff_id'],
            'status' => 'ready',
        ]);

        return back()->with('success', 'Counter created successfully.');
    }

    public function updateCounter(Request $request, $id)
    {
        $counter = Counter::findOrFail($id);

        $validated = $request->validate([
            'department_id' => 'required|exists:departments,id',
            'name' => 'required|string|max:255',
            'assigned_staff_id' => 'nullable|exists:users,id',
        ]);

        $counter->update([
            'department_id' => $validated['department_id'],
            'name' => $validated['name'],
            'assigned_staff_id' => $validated['assigned_staff_id'],
        ]);

        return back()->with('success', 'Counter updated successfully.');
    }

    public function deleteCounter($id)
    {
        $counter = Counter::findOrFail($id);
        $counter->update([
            'assigned_staff_id' => null,
            'current_queue_id' => null,
            'status' => 'unavailable',
        ]);
        $counter->delete();

        return back()->with('success', 'Counter archived successfully.');
    }

    public function archivedCounters()
    {
        $items = Counter::onlyTrashed()
            ->with(['department'])
            ->orderByDesc('deleted_at')
            ->get();

        return view('admin.archive', [
            'title' => 'Archived counters',
            'eyebrow' => 'Counter Archive',
            'description' => 'Counters moved out of active queue assignment.',
            'backRoute' => route('admin.counters'),
            'restoreRouteName' => 'admin.counters.restore',
            'items' => $items,
            'columns' => ['Counter', 'Department', 'Status'],
            'type' => 'counters',
        ]);
    }

    public function restoreCounter($id)
    {
        $counter = Counter::onlyTrashed()->findOrFail($id);
        $counter->restore();
        $counter->update(['status' => 'ready']);

        return back()->with('success', 'Counter restored successfully.');
    }
}
