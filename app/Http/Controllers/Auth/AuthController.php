<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * แสดงหน้าล็อกอิน
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * ประมวลผลการล็อกอิน
     * (จำกัดอัตราการลองผิดได้ผ่าน throttle middleware ที่ผูกกับ route POST /login)
     */
    public function login(Request $request)
    {
        $request->validate([
            'email'    => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        $credentials = $request->only('email', 'password');
        $remember = $request->boolean('remember');

        if (! Auth::attempt($credentials, $remember)) {
            throw ValidationException::withMessages([
                'email' => 'อีเมลหรือรหัสผ่านไม่ถูกต้อง',
            ]);
        }

        // บัญชีถูกปิดการใช้งาน
        if (! Auth::user()->isActive()) {
            Auth::logout();

            throw ValidationException::withMessages([
                'email' => 'บัญชีนี้ถูกปิดการใช้งาน กรุณาติดต่อผู้ดูแลระบบ',
            ]);
        }

        $request->session()->regenerate();

        return redirect()->intended(RouteServiceProvider::HOME);
    }

    /**
     * ออกจากระบบ
     */
    public function logout(Request $request)
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
