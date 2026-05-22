<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable; // class dùng cho login
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    protected $table = 'users'; // tên bảng trong database

    protected $primaryKey = 'user_id'; // khóa chính không phải id mà là user_id

    public $incrementing = true; // khóa chính tự tăng
    
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
        'permissions',
        'id_code',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'permissions' => 'array',
        ];
    }

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