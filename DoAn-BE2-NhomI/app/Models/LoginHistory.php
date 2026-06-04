<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoginHistory extends Model
{
    protected $table = 'login_history';

    protected $primaryKey = 'history_id';

    protected $fillable = [
        'user_id',
        'email',
        'login_time',
        'logout_time',
        'ip_address',
        'status',
    ];

    // Quan hệ với bảng users
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }
}