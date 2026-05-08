<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReviewImage extends Model
{
    protected $table = 'review_images';
    protected $primaryKey = 'image_id';
    public $timestamps = false;

    protected $fillable = ['review_id', 'image_url', 'sort_order'];

    public function review()
    {
        return $this->belongsTo(Review::class, 'review_id', 'review_id');
    }
}
