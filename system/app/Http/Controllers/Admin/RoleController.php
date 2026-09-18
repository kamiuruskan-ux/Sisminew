<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\Permission;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function index(Request $request)
    {
        $query = Role::adminOnly()->withCount('users');

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('slug', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $roles = $query->latest()->paginate(15);
        return view('admin.roles.index', compact('roles'));
    }

    public function create()
    {
        $this->ensureDashboardPermissions();
        $permissions = Permission::all()->groupBy('group');
        return view('admin.roles.create', compact('permissions'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:roles,name',
            'slug' => 'nullable|string|max:255|unique:roles,slug',
            'description' => 'nullable|string',
            'permissions' => 'nullable|array',
            'dashboard_permission' => 'nullable',
        ]);

        $validated['slug'] = $validated['slug'] ?? \Illuminate\Support\Str::slug($validated['name']);

        $role = Role::create($validated);

        // Assign permissions
        $permissionIds = $validated['permissions'] ?? [];
        if (!empty($request->dashboard_permission)) {
            $permissionIds[] = $request->dashboard_permission;
        }
        $role->permissions()->sync(array_unique(array_filter($permissionIds)));

        return redirect()->route('admin.roles.index')
            ->with('success', 'Role berhasil ditambahkan.');
    }

    public function show(Role $role)
    {
        if ($role->isNonAdminRole()) {
            return redirect()->route('admin.roles.index')
                ->with('error', 'Role ini diperuntukkan untuk portal khusus.');
        }

        return view('admin.roles.show', compact('role'));
    }

    public function edit($encodedId)
    {
        $id = decode_id($encodedId);
        $role = Role::with('permissions')->findOrFail($id);

        if ($role->isSuperAdmin()) {
            return redirect()->route('admin.roles.index')
                ->with('error', 'Role Super Admin adalah role utama sistem dan tidak dapat diubah.');
        }

        if ($role->isNonAdminRole()) {
            return redirect()->route('admin.roles.index')
                ->with('error', 'Role ini diperuntukkan untuk portal khusus dan tidak dikelola melalui Admin Roles.');
        }

        $this->ensureDashboardPermissions();
        $permissions = Permission::all()->groupBy('group');
        return view('admin.roles.edit', compact('role', 'permissions'));
    }

    private function ensureDashboardPermissions()
    {
        $dashboards = [
            ['name' => 'View Dashboard Admin',       'slug' => 'view-dashboard-admin',       'group' => 'dashboard'],
            ['name' => 'View Dashboard Bendahara',   'slug' => 'view-dashboard-bendahara',   'group' => 'dashboard'],
            ['name' => 'View Dashboard Guru',        'slug' => 'view-dashboard-guru',        'group' => 'dashboard'],
            ['name' => 'View Dashboard BK',          'slug' => 'view-dashboard-bk',          'group' => 'dashboard'],
            ['name' => 'View Dashboard Operator TU', 'slug' => 'view-dashboard-operator',    'group' => 'dashboard'],
            ['name' => 'View Dashboard Staff',       'slug' => 'view-dashboard-staff',       'group' => 'dashboard'],
        ];

        foreach ($dashboards as $d) {
            Permission::firstOrCreate(['slug' => $d['slug']], $d);
        }
    }

    public function update(Request $request, $encodedId)
    {
        $id = decode_id($encodedId);
        $role = Role::findOrFail($id);

        if ($role->isSuperAdmin()) {
            return redirect()->route('admin.roles.index')
                ->with('error', 'Role Super Admin adalah role utama sistem dan tidak dapat diubah.');
        }

        if ($role->isNonAdminRole()) {
            return redirect()->route('admin.roles.index')
                ->with('error', 'Role ini diperuntukkan untuk portal khusus dan tidak dikelola melalui Admin Roles.');
        }

        if ($role->isProtectedSystemRole()) {
            // Keep original name and slug for system protected roles
            $validated = $request->validate([
                'description' => 'nullable|string',
                'permissions' => 'nullable|array',
                'dashboard_permission' => 'nullable',
            ]);
            $validated['name'] = $role->name;
            $validated['slug'] = $role->slug;
        } else {
            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:roles,name,' . $role->id,
                'slug' => 'nullable|string|max:255|unique:roles,slug,' . $role->id,
                'description' => 'nullable|string',
                'permissions' => 'nullable|array',
                'dashboard_permission' => 'nullable',
            ]);
            $validated['slug'] = $validated['slug'] ?? \Illuminate\Support\Str::slug($validated['name']);
        }

        $role->update($validated);

        // Sync permissions
        $permissionIds = $validated['permissions'] ?? [];
        if (!empty($request->dashboard_permission)) {
            $permissionIds[] = $request->dashboard_permission;
        }
        $role->permissions()->sync(array_unique(array_filter($permissionIds)));

        return redirect()->route('admin.roles.index')
            ->with('success', 'Role berhasil diperbarui.');
    }

    public function destroy($encodedId)
    {
        $id = decode_id($encodedId);
        $role = Role::findOrFail($id);
        
        // Prevent deleting protected system roles
        if ($role->isProtectedSystemRole()) {
            return back()->with('error', 'Role bawaan sistem tidak dapat dihapus.');
        }

        if ($role->isNonAdminRole()) {
            return back()->with('error', 'Role portal khusus tidak dapat dihapus melalui Admin Roles.');
        }

        $role->delete();

        return back()->with('success', 'Role berhasil dihapus.');
    }
}
