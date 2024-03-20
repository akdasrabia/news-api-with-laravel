<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\News;

class Comment extends Model
{
    use HasFactory;


    protected $fillable = [
        'content',
        'user_id',
        'news_id',
        'is_deleted'
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function news(){
        return $this->belongsTo(News::class);
    }
}
