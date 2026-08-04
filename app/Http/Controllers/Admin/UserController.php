<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    /**
     * รายการผู้ใช้เจ้าหน้าที่
     */
    public function index()
    {
        $users = User::orderBy('name')->paginate(15);

        return view('admin.users.index', compact('users'));
    }

    /**
     * ฟอร์มสร้างผู้ใช้ใหม่
     */
    public function create()
    {
        return view('admin.users.create');
    }

    /**
     * บันทึกผู้ใช้ใหม่
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name'     => ['required', 'string', 'max:150'],
            'email'    => ['required', 'string', 'email', 'max:150', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role_id'  => ['nullable', 'exists:roles,role_id'],
        ]);

        $user = User::create([
            'name'      => $data['name'],
            'email'     => $data['email'],
            'password'  => $data['password'], // ผ่าน cast 'hashed' อัตโนมัติ
            'is_active' => true,
        ]);

        if (! empty($data['role_id'])) {
            $user->roles()->sync($data['role_id']);
        }

        return redirect()
            ->route('admin.users.index')
            ->with('success', "เพิ่มผู้ใช้ {$data['name']} เรียบร้อยแล้ว");
    }

    /**
     * ฟอร์มแก้ไขผู้ใช้
     */
    public function edit(User $user)
    {
        $roles = Role::orderBy('label')->get();

        return view('admin.users.edit', compact('user', 'roles'));
    }

    /**
     * บันทึกการแก้ไขผู้ใช้
     */
    public function update(Request $request, User $user)
    {
        $request->merge(['password' => $request->password ?: null]);

        $data = $request->validate([
            'name'     => ['required', 'string', 'max:150'],
            'email'    => ['required', 'string', 'email', 'max:150', 'unique:users,email,' . $user->id],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'role_id'  => ['nullable', 'exists:roles,role_id'],
        ]);

        $user->name = $data['name'];
        $user->email = $data['email'];
        if (! empty($data['password'])) {
            $user->password = $data['password']; // ผ่าน cast 'hashed'
        }
        $user->is_active = $request->boolean('is_active');
        $user->save();

        $user->roles()->sync($data['role_id'] ?? []);

        return redirect()
            ->route('admin.users.index')
            ->with('success', "แก้ไขผู้ใช้ {$user->name} เรียบร้อยแล้ว");
    }

    /**
     * เปิด/ปิดการใช้งานบัญชี (ไม่สามารถปิดบัญชีของตนเองได้)
     */
    public function toggleActive(Request $request, User $user)
    {
        if ($user->id === Auth::id()) {
            return back()->with('error', 'ไม่สามารถปิดการใช้งานบัญชีของตนเองได้');
        }

        $user->is_active = ! $user->is_active;
        $user->save();

        $label = $user->is_active ? 'เปิดการใช้งาน' : 'ปิดการใช้งาน';
        $name = $user->name;

        return back()->with('success', "{$label}บัญชี {$name} เรียบร้อยแล้ว");
    }
}
