<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // ── Roles ──────────────────────────────────────────
        $adminRole = Role::updateOrCreate(
            ['name' => 'admin'],
            ['label' => 'ผู้ดูแลระบบ', 'description' => 'ผู้ดูแลระบบเต็มรูปแบบ', 'is_system' => true],
        );

        $staffRole = Role::updateOrCreate(
            ['name' => 'staff'],
            ['label' => 'เจ้าหน้าที่', 'description' => 'เจ้าหน้าที่สำนักคอมพิวเตอร์', 'is_system' => true],
        );

        // ── Permissions ────────────────────────────────────
        $permissions = [
            // User management
            ['name' => 'users.view',    'label' => 'ดูรายชื่อผู้ใช้',       'group' => 'users'],
            ['name' => 'users.create',  'label' => 'สร้างผู้ใช้',           'group' => 'users'],
            ['name' => 'users.edit',    'label' => 'แก้ไขผู้ใช้',           'group' => 'users'],
            ['name' => 'users.delete',  'label' => 'ลบผู้ใช้',             'group' => 'users'],
            ['name' => 'users.toggle',  'label' => 'เปิด/ปิดการใช้งาน',    'group' => 'users'],

            // Role & Permission management
            ['name' => 'roles.view',        'label' => 'ดูบทบาท',          'group' => 'roles'],
            ['name' => 'roles.create',      'label' => 'สร้างบทบาท',        'group' => 'roles'],
            ['name' => 'roles.edit',        'label' => 'แก้ไขบทบาท',        'group' => 'roles'],
            ['name' => 'roles.delete',      'label' => 'ลบบทบาท',          'group' => 'roles'],
            ['name' => 'permissions.view',  'label' => 'ดูสิทธิ์',           'group' => 'permissions'],
            ['name' => 'permissions.create', 'label' => 'สร้างสิทธิ์',       'group' => 'permissions'],
            ['name' => 'permissions.edit',  'label' => 'แก้ไขสิทธิ์',       'group' => 'permissions'],
            ['name' => 'permissions.delete', 'label' => 'ลบสิทธิ์',         'group' => 'permissions'],

            // Service requests
            ['name' => 'requests.view',     'label' => 'ดูรายการคำขอ',     'group' => 'requests'],
            ['name' => 'requests.approve',  'label' => 'อนุมัติคำขอ',       'group' => 'requests'],
            ['name' => 'requests.reject',   'label' => 'ปฏิเสธคำขอ',       'group' => 'requests'],
            ['name' => 'requests.delete',   'label' => 'ลบคำขอ',           'group' => 'requests'],

            // Service accounts
            ['name' => 'accounts.view',     'label' => 'ดูบัญชีผู้ใช้บริการ', 'group' => 'accounts'],
            ['name' => 'accounts.create',   'label' => 'สร้างบัญชีผู้ใช้บริการ', 'group' => 'accounts'],
            ['name' => 'accounts.edit',     'label' => 'แก้ไขบัญชีผู้ใช้บริการ', 'group' => 'accounts'],
            ['name' => 'accounts.delete',   'label' => 'ลบบัญชีผู้ใช้บริการ', 'group' => 'accounts'],
            ['name' => 'accounts.toggle',   'label' => 'เปิด/ปิดบัญชี',      'group' => 'accounts'],
        ];

        foreach ($permissions as $perm) {
            Permission::updateOrCreate(['name' => $perm['name']], $perm);
        }

        // ── Assign permissions to roles ────────────────────
        $adminPermissions = Permission::pluck('permission_id')->all();
        $adminRole->permissions()->sync($adminPermissions);

        $staffPermissions = Permission::whereIn('name', [
            'requests.view',
            'requests.approve',
            'requests.reject',
            'requests.delete',
            'accounts.view',
            'accounts.create',
            'accounts.edit',
            'accounts.delete',
            'accounts.toggle',
        ])->pluck('permission_id')->all();

        $staffRole->permissions()->sync($staffPermissions);

        // ── Assign admin role to the first user (if any) ──
        $firstUser = \App\Models\User::first();
        if ($firstUser) {
            $firstUser->roles()->syncWithoutDetaching($adminRole);
        }
    }
}
