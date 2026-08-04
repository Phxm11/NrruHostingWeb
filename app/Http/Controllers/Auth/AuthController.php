<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
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

    /**
     * แสดงฟอร์มขอลิงก์รีเซ็ตรหัสผ่าน
     */
    public function showForgotPasswordForm()
    {
        return view('auth.forgot-password');
    }

    /**
     * ส่งอีเมลลิงก์สำหรับรีเซ็ตรหัสผ่าน
     */
    public function sendResetLinkEmail(Request $request)
    {
        $request->validate([
            'email' => ['required', 'string', 'email'],
        ]);

        $status = Password::sendResetLink(
            $request->only('email')
        );

        // ไม่ยืนยัน/ปฏิเสธว่ามีอีเมลนี้อยู่ในระบบหรือไม่ เพื่อความปลอดภัย
        // จึงแสดงข้อความสำเร็จเสมอเมื่อรูปแบบอีเมลถูกต้อง
        if ($status === Password::RESET_LINK_SENT) {
            return back()->with('status', 'หากอีเมลนี้มีอยู่ในระบบ เราได้ส่งลิงก์สำหรับรีเซ็ตรหัสผ่านไปให้แล้ว');
        }

        if ($status === Password::RESET_THROTTLED) {
            throw ValidationException::withMessages([
                'email' => 'คุณเพิ่งขอลิงก์รีเซ็ตรหัสผ่านไปแล้ว กรุณารอสักครู่แล้วลองใหม่',
            ]);
        }

        return back()->with('status', 'หากอีเมลนี้มีอยู่ในระบบ เราได้ส่งลิงก์สำหรับรีเซ็ตรหัสผ่านไปให้แล้ว');
    }

    /**
     * แสดงฟอร์มตั้งรหัสผ่านใหม่
     */
    public function showResetPasswordForm(Request $request, string $token)
    {
        return view('auth.reset-password', [
            'token' => $token,
            'email' => $request->query('email'),
        ]);
    }

    /**
     * ประมวลผลการตั้งรหัสผ่านใหม่
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'token'    => ['required'],
            'email'    => ['required', 'string', 'email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user) use ($request) {
                $user->forceFill([
                    'password' => bcrypt($request->password),
                ])->setRememberToken(Str::random(60));

                $user->save();
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return redirect()->route('login')->with('status', 'ตั้งรหัสผ่านใหม่เรียบร้อยแล้ว กรุณาเข้าสู่ระบบ');
        }

        throw ValidationException::withMessages([
            'email' => $status === Password::INVALID_TOKEN
                ? 'ลิงก์รีเซ็ตรหัสผ่านนี้ไม่ถูกต้องหรือหมดอายุแล้ว'
                : 'ไม่พบบัญชีผู้ใช้ที่ตรงกับอีเมลนี้',
        ]);
    }
}
