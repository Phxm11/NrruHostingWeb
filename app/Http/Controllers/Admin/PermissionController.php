<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use Illuminate\Http\Request;

class PermissionController extends Controller
{
    /**
     * รายการสิทธิ์
     */
    public function index()
    {
        $permissions = Permission::orderBy('group')->orderBy('label')->get();
        $groups = Permission::whereNotNull('group')
            ->orderBy('group')
            ->pluck('group')
            ->unique();

        return view('admin.permissions.index', compact('permissions', 'groups'));
    }

    /**
     * ฟอร์มสร้างสิทธิ์ใหม่
     */
    public function create()
    {
        return view('admin.permissions.create');
    }

    /**
     * บันทึกสิทธิ์ใหม่
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name'        => ['required', 'string', 'max:100', 'unique:permissions,name'],
            'label'       => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string'],
            'group'       => ['nullable', 'string', 'max:50'],
        ]);

        Permission::create($data);

        return redirect()
            ->route('admin.permissions.index')
            ->with('success', 'สร้างสิทธิ์เรียบร้อยแล้ว');
    }

    /**
     * ฟอร์มแก้ไขสิทธิ์
     */
    public function edit(Permission $permission)
    {
        return view('admin.permissions.edit', compact('permission'));
    }

    /**
     * บันทึกการแก้ไขสิทธิ์
     */
    public function update(Request $request, Permission $permission)
    {
        $data = $request->validate([
            'label'       => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string'],
            'group'       => ['nullable', 'string', 'max:50'],
        ]);

        $permission->update($data);

        return redirect()
            ->route('admin.permissions.index')
            ->with('success', 'แก้ไขสิทธิ์เรียบร้อยแล้ว');
    }

    /**
     * ลบสิทธิ์
     */
    public function destroy(Permission $permission)
    {
        $permissionName = $permission->label;
        $permission->delete();

        return redirect()
            ->route('admin.permissions.index')
            ->with('success', "ลบสิทธิ์ {$permissionName} เรียบร้อยแล้ว");
    }
}
