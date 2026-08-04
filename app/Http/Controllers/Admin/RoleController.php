<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    /**
     * รายการบทบาท
     */
    public function index()
    {
        $roles = Role::withCount('users')->withCount('permissions')->orderBy('name')->get();

        return view('admin.roles.index', compact('roles'));
    }

    /**
     * ฟอร์มสร้างบทบาทใหม่
     */
    public function create()
    {
        $permissions = Permission::orderBy('group')->orderBy('label')->get();

        return view('admin.roles.create', compact('permissions'));
    }

    /**
     * บันทึกบทบาทใหม่
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name'        => ['required', 'string', 'max:50', 'unique:roles,name'],
            'label'       => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string'],
            'permissions' => ['array'],
            'permissions.*' => ['exists:permissions,permission_id'],
        ]);

        $role = Role::create([
            'name'        => $data['name'],
            'label'       => $data['label'],
            'description' => $data['description'] ?? null,
        ]);

        if (! empty($data['permissions'])) {
            $role->permissions()->sync($data['permissions']);
        }

        return redirect()
            ->route('admin.roles.index')
            ->with('success', "สร้างบทบาท {$role->label} เรียบร้อยแล้ว");
    }

    /**
     * ฟอร์มแก้ไขบทบาท
     */
    public function edit(Role $role)
    {
        $permissions = Permission::orderBy('group')->orderBy('label')->get();

        return view('admin.roles.edit', compact('role', 'permissions'));
    }

    /**
     * บันทึกการแก้ไขบทบาท
     */
    public function update(Request $request, Role $role)
    {
        $data = $request->validate([
            'label'       => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string'],
            'permissions' => ['array'],
            'permissions.*' => ['exists:permissions,permission_id'],
        ]);

        $role->update([
            'label'       => $data['label'],
            'description' => $data['description'] ?? null,
        ]);

        $role->permissions()->sync($data['permissions'] ?? []);

        return redirect()
            ->route('admin.roles.index')
            ->with('success', "แก้ไขบทบาท {$role->label} เรียบร้อยแล้ว");
    }

    /**
     * ลบบทบาท
     */
    public function destroy(Role $role)
    {
        if ($role->is_system) {
            return back()->with('error', 'ไม่สามารถลบบทบาทระบบได้');
        }

        $roleName = $role->label;
        $role->delete();

        return redirect()
            ->route('admin.roles.index')
            ->with('success', "ลบบทบาท {$roleName} เรียบร้อยแล้ว");
    }
}
