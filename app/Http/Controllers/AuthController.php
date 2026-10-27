<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }
    public function login(Request $request)
    {
        $credentials = $request->validate(['email' => 'required|email', 'password' => 'required|string']);
        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            $redirect = $request->input('redirect');
            if (is_string($redirect) && str_starts_with($redirect, '/') && !str_starts_with($redirect, '//')) return redirect($redirect)->with('success', 'Đăng nhập thành công!');
            return redirect()->intended(route('home'))->with('success', 'Đăng nhập thành công!');
        }
        return back()->withErrors([
            'email' => 'Email hoặc mật khẩu không đúng. Vui lòng thử lại!'
        ])->onlyInput('email');
    }
    public function showRegister()
    {
        return view('auth.register');
    }
    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:120',
            'email' => 'required|email|max:180|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
        ], [
            'name.required' => 'Vui lòng nhập họ và tên.',
            'name.max' => 'Họ và tên không được vượt quá 120 ký tự.',

            'email.required' => 'Vui lòng nhập địa chỉ email.',
            'email.email' => 'Địa chỉ email không hợp lệ.',
            'email.max' => 'Email không được vượt quá 180 ký tự.',
            'email.unique' => 'Email này đã được đăng ký. Vui lòng sử dụng email khác.',

            'password.required' => 'Vui lòng nhập mật khẩu.',
            'password.min' => 'Mật khẩu phải có ít nhất 8 ký tự.',
            'password.confirmed' => 'Mật khẩu nhập lại không khớp.',
        ]);

        $role = User::query()->count() === 0 ? 'admin' : 'customer';

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => $role,
        ]);

        Auth::login($user);
        $request->session()->regenerate();
        return redirect()->route('home')->with('success', $role === 'admin'
            ? 'Tạo tài khoản thành công! Đây là tài khoản quản trị viên đầu tiên.'
            : 'Tạo tài khoản thành công!');
    }
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('home')->with('success', 'Bạn đã đăng xuất.');
    }
}
