<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BackupLog extends Model
{
    protected $table = 'backup_logs';
    protected $primaryKey = 'backup_id';
    public $timestamps = false; // Bảng này dùng `created_at` tự tạo trong MySQL hoặc thủ công

    protected $fillable = [
        'file_name',
        'file_path',
        'file_size',
        'status',
        'created_by',
        'created_at'
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by', 'user_id');
    }
}
