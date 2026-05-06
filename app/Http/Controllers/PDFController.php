<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class PDFController extends Controller
{
    /**
     * Export users by role as PDF
     *
     * @param  string|null  $role  Role name (admin|staff|doctor) or null for all users
     */
    public function exportUsers(Request $request, ?string $role = null)
    {
        // Build query with eager loading to avoid N+1
        $query = User::with(['role', 'department', 'assignedCounters']);

        if ($role) {
            $roleMap = [
                'admin' => 1,
                'staff' => 2,
                'doctor' => 3,
            ];

            if (isset($roleMap[$role])) {
                $query->where('role_id', $roleMap[$role]);
            }
        }

        $users = $query->orderBy('name')->get();

        $roleName = $role ? ucfirst($role) : 'All Users';
        $fileName = $role ? $role.'-list.pdf' : 'users-list.pdf';

        $data = [
            'title' => $roleName.' List',
            'date' => now()->format('m/d/Y'),
            'users' => $users,
            'role' => $roleName,
        ];

        $pdf = Pdf::loadView('pdf.users', $data);

        return $pdf->download($fileName);
    }
}
