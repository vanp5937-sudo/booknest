<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $users = User::query()->when($request->filled('q'), fn($q) => $q->where(fn($w) => $w->where('name', 'like', '%' . $request->q . '%')->orWhere('email', 'like', '%' . $request->q . '%')))->when($request->filled('role'), fn($q) => $q->where('role', $request->role))->orderBy('id')->paginate(15)->withQueryString();
        return view('admin.users.index', compact('users'));
    }
    public function update(Request $request, User $user)
    {
        $data = $request->validate(['role' => 'required|in:admin,customer']);
        if ($user->id === auth()->id() && $data['role'] !== 'admin') return back()->with('error', 'Bạn không thể tự hạ quyền tài khoản đang đăng nhập.');
        if ($user->role === 'admin' && $data['role'] !== 'admin' && User::where('role', 'admin')->count() <= 1) return back()->with('error', 'Không thể hạ quyền quản trị viên cuối cùng.');
        $user->update(['role' => $data['role']]);
        return back()->with('success', 'Đã cập nhật quyền tài khoản “' . $user->name . '” thành ' . ($data['role'] === 'admin' ? 'quản trị viên' : 'khách hàng') . '.');
    }
    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) return back()->with('error', 'Không thể xóa tài khoản đang đăng nhập.');
        if ($user->role === 'admin' && User::where('role', 'admin')->count() <= 1) return back()->with('error', 'Không thể xóa quản trị viên cuối cùng.');
        $name = $user->name;
        $user->delete();
        return back()->with('success', 'Đã xóa tài khoản “' . $name . '” thành công.');
    }
}
