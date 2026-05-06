<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rules;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

use App\Models\Role;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        $roleKeyColumn = $this->roleKeyColumn();
        $roleNameColumn = $this->roleNameColumn();

        foreach ([
            ['name' => 'Administrator', 'description' => 'System Administrator'],
        ] as $roleData) {
            $exists = DB::table('roles')->where($roleNameColumn, $roleData['name'])->exists();

            if (!$exists) {
                $insert = [$roleNameColumn => $roleData['name']];

                if (Schema::hasColumn('roles', 'description')) {
                    $insert['description'] = $roleData['description'];
                }

                DB::table('roles')->insert($insert);
            }
        }

        $roles = DB::table('roles')
            ->select([
                DB::raw("{$roleKeyColumn} as id"),
                DB::raw("{$roleNameColumn} as name"),
            ])
            ->whereIn($roleNameColumn, ['Administrator', 'Admin'])
            ->orderBy($roleNameColumn)
            ->get();

        $departments = collect(); // No departments for admin

        return view('auth.register', compact('roles', 'departments'));
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $roleKeyColumn = $this->roleKeyColumn();
        $roleNameColumn = $this->roleNameColumn();

        $allowedRoleIds = DB::table('roles')
            ->whereIn($roleNameColumn, ['Administrator', 'Admin'])
            ->pluck($roleKeyColumn)
            ->all();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role_id' => ['required', "exists:roles,{$roleKeyColumn}", Rule::in($allowedRoleIds)],
        ]);

        $selectedRole = DB::table('roles')
            ->where($roleKeyColumn, $validated['role_id'])
            ->first();

        if (!$selectedRole) {
            throw ValidationException::withMessages([
                'role_id' => 'Selected role does not exist.',
            ]);
        }

        $selectedRoleName = $selectedRole->{$roleNameColumn} ?? null;

        if (!in_array($selectedRoleName, ['Administrator', 'Admin'], true)) {
            throw ValidationException::withMessages([
                'role_id' => 'Only administrator accounts can be registered.',
            ]);
        }

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role_id' => $validated['role_id'],
            'department_id' => null,
        ]);

        event(new Registered($user));

        return redirect()
            ->route('login')
            ->with('success', 'Administrator account created successfully. Please log in.');
    }



    private function roleNameColumn(): string
    {
        return Schema::hasColumn('roles', 'name') ? 'name' : 'role_name';
    }

    private function roleKeyColumn(): string
    {
        return Schema::hasColumn('roles', 'id') ? 'id' : 'role_id';
    }

    private function departmentNameColumn(): string
    {
        return Schema::hasColumn('departments', 'name') ? 'name' : 'department_name';
    }

    private function departmentKeyColumn(): string
    {
        return Schema::hasColumn('departments', 'id') ? 'id' : 'department_id';
    }
}
