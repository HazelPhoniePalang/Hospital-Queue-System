<?php

namespace App\Http\Controllers;

use App\Models\Counter;
use App\Models\Department;
use App\Models\Role;
use App\Models\Service;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
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
        User::findOrFail($id)->delete();

        return back()->with('success', 'User deleted successfully.');
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

        return back()->with('success', 'Department deleted successfully.');
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

        return back()->with('success', 'Service deleted successfully.');
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
        Counter::findOrFail($id)->delete();

        return back()->with('success', 'Counter deleted successfully.');
    }
}
