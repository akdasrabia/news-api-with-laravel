<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Comment;
use App\Notifications\NewNewsCreatedNotification;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Notification;

class News extends Model
{
    use HasFactory, Notifiable;

    protected static function boot()
    {
        parent::boot();

        static::created(function ($news) {
            $users = User::get();
            foreach($users as $user){
                Notification::send( $user, new NewNewsCreatedNotification($news));

            }
       
        });
    }


    protected $fillable = [
        'title',
        'content',
        'user_id',
        'image',
        'is_deleted',
        'slug'
    ];


    public function user(){
        return $this->belongsTo(User::class);
    }

    public function comments(){
        return $this->hasMany(Comment::class);
    }

}
