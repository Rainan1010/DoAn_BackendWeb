<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable; // class dùng cho login
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    protected $table = 'users'; // tên bảng trong database

    protected $primaryKey = 'user_id'; // khóa chính không phải id mà là user_id

    public $incrementing = true; // khóa chính tự tăng
    const UPDATED_AT = null; // Bỏ qua cập nhật cột updated_at do DB không có
    // các cột cho phép insert/update
    protected $fillable = [
        'email',
        'password_hash',
        'full_name',
        'phone',
        'avatar_url',
        'role',
        'provider',
        'provider_id',
        'is_active',
        'is_verified',
    ];

    // ẩn khi trả dữ liệu ra ngoài
    protected $hidden = [
        'password_hash',
        'remember_token',
    ];

    //cho Laravel biết password nằm ở đâu
    public function getAuthPassword()
    {
        return $this->password_hash; // dùng cột password_hash thay vì password
    }
}