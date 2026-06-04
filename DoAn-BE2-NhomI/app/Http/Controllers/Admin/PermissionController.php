<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\User;

class PermissionController extends Controller
{
    private const USER_NOT_FOUND_MESSAGE = 'Nhân sự này không còn tồn tại hoặc đã bị người khác xóa. Vui lòng tải lại danh sách.';

    private function findUser(string $id): ?User
    {
        return User::find($id);
    }

    private function userNotFoundRedirect()
    {
        return redirect()
            ->route('admin.permissions.index')
            ->with('error', self::USER_NOT_FOUND_MESSAGE);
    }

    public function index()
    {
        $users = User::all();
        $stats = [
            'total' => $users->count(),
            'admins' => $users->where('role', 'admin')->count(),
            'staff'  => $users->where('role', 'staff')->count(),
            'inactive' => $users->where('is_active', false)->count(),
        ];
        return view('admin.permissions.index', compact('users', 'stats'));
    }

    public function toggleStatus(string $id)
    {
        $user = $this->findUser($id);
        if (!$user) {
            return $this->userNotFoundRedirect();
        }
        $user->is_active = !$user->is_active;
        $user->save();

        $status = $user->is_active ? 'mở khóa' : 'khóa';
        return back()->with('success', "Tài khoản đã được {$status} thành công!");
    }

    public function show(string $id)
    {
        $user = $this->findUser($id);
        if (!$user) {
            return $this->userNotFoundRedirect();
        }
        return view('admin.permissions.show', compact('user'));
    }

    public function edit(string $id)
    {
        $user = $this->findUser($id);
        if (!$user) {
            return $this->userNotFoundRedirect();
        }
        return view('admin.permissions.edit', compact('user'));
    }

    public function create()
    {
        return view('admin.permissions.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'full_name' => 'required|string|max:255',
            'email'     => 'required|email|unique:users,email',
            'role'      => 'required|in:admin,staff,user',
            'password'  => 'required|min:6',
        ], [
            'full_name.required' => 'Vui lòng nhập họ và tên.',
            'full_name.string'   => 'Họ và tên phải là chuỗi ký tự hợp lệ.',
            'full_name.max'      => 'Họ và tên không được vượt quá 255 ký tự.',

            'email.required'     => 'Vui lòng nhập địa chỉ email.',
            'email.email'        => 'Địa chỉ email không đúng định dạng.',
            'email.unique'       => 'Email này đã được sử dụng bởi tài khoản khác.',

            'role.required'      => 'Vui lòng chọn vai trò cho nhân sự.',
            'role.in'            => 'Vai trò không hợp lệ. Chỉ được chọn: Admin, Staff hoặc User.',

            'password.required'  => 'Vui lòng nhập mật khẩu.',
            'password.min'       => 'Mật khẩu phải có ít nhất 6 ký tự.',
        ]);

        User::create([
            'full_name'     => $request->full_name,
            'email'          => $request->email,
            'role'           => $request->role,
            'password_hash' => bcrypt($request->password),
            'permissions'    => $request->permissions,
            'id_code'        => 'USR-' . time(),
            'is_active'      => true,
        ]);

        return redirect()->route('admin.permissions.index')->with('success', 'Thêm nhân sự mới thành công!');
    }

    public function update(Request $request, string $id)
    {
        $user = $this->findUser($id);
        if (!$user) {
            return redirect()
                ->route('admin.permissions.index')
                ->with('error', 'Tài khoản này không còn tồn tại. Có thể đã bị người khác xóa trước đó.');
        }

        $request->validate([
            'role' => 'nullable|in:admin,staff,user',
        ], [
            'role.in' => 'Vai trò không hợp lệ. Chỉ được chọn: Admin, Staff hoặc User.',
        ]);

        // Kiểm tra xung đột: nếu bản ghi đã bị sửa bởi người khác
        $lastUpdatedAt = $request->input('last_updated_at');
        if ($lastUpdatedAt && $user->updated_at) {
            $dbTimestamp   = $user->updated_at->format('Y-m-d H:i:s');
            $formTimestamp = date('Y-m-d H:i:s', strtotime($lastUpdatedAt));

            if ($dbTimestamp !== $formTimestamp) {
                return redirect()
                    ->back()
                    ->withInput()
                    ->with('error', '⚠️ Xung đột dữ liệu: Tài khoản này vừa được người khác chỉnh sửa trong lúc bạn đang làm việc. Vui lòng tải lại trang để xem thông tin mới nhất trước khi lưu.');
            }
        }

        $user->update([
            'permissions' => $request->permissions,
            'role'        => $request->role ?? $user->role,
        ]);

        return redirect()->route('admin.permissions.index')->with('success', 'Cập nhật phân quyền thành công!');
    }

    public function destroy(string $id)
    {
        $user = $this->findUser($id);
        if (!$user) {
            return $this->userNotFoundRedirect();
        }
        $user->delete();

        return redirect()->route('admin.permissions.index')->with('success', 'Xóa nhân sự thành công!');
    }
}
