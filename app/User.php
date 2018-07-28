<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    public function microposts()
    {
        return $this->hasMany(Micropost::class);
    }
    
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];
    
    public function followings()
    {
        return $this->belongsToMany(User::class, 'user_follow', 'user_id', 'follow_id')->withTimestamps();
    }

    public function followers()
    {
        return $this->belongsToMany(User::class, 'user_follow', 'follow_id', 'user_id')->withTimestamps();
    }
    
    public function follow($userId)
    {
        $exist = $this->is_following($userId);
        
        $its_me = $this->id == $userId;
        
        if ($exist || $its_me) {
            
            return false;
        } else {
            
            $this->followings()->attach($userId);
            return true;
        }
    }
    
    public function unfollow($userId)
    {
        $exist = $this->is_following($userId);
        
        $its_me = $this->id == $userId;
        
        if ($exist && !$its_me) {
            
            $this->followings()->detach($userId);
            return true;
        } else {
            return false;
        }
    }
    
    public function is_following($userId) {
        return $this->followings()->where('follow_id', $userId)->exists();
    }
    
    public function feed_microposts()
    {
        $follow_user_ids = $this->followings()-> pluck('users.id')->toArray();
        $follow_user_ids[] = $this->id;
        return Micropost::whereIn('user_id', $follow_user_ids);
    }
    
    public function favorites()
    {
        return $this->belongsToMany(User::class, 'micropost_favorite', 'user_id', 'micropost_id')->withTimestamps();
    }
    
    public function favorite($userId)
    {
        $exist = $this->is_favorite($userId);
        $its_me = $this->id == $userId;
        
        if ($exist || $its_me) {
            return false;
        }else{
            $this->favorites()->attach($userId);
            return true;
        }
    }
    
    public function unfavorite($userId)
    {
       $exist = $this->is_favorite($userId);
        $its_me = $this->id == $userId;
        
        if ($exist && !$its_me) {
            $this->favorites()->detach($userId);
            return true;
        }else{
            return false;
        }
    }
    
    public function is_favorite($userId) {
        return $this->favorites()->where('micropost_id', $userId)->exists();
    }
}
