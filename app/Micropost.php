<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Micropost extends Model
{
    protected $fillable = ['content', 'user_id', 'micropost_id'];
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    public function favorites()
    {
        return $this->belongsToMany(Micropost::class, 'micropost_favorite', 'user_id', 'micropost_id')->withTimestamps();
    }
    
    public function favorite($micropostId)
    {
        $exist = $this->is_favorite($micropostId);
        $its_micropost = $this->id == $micropostId;
        
        if ($exist || $its_micropost) {
            return false;
        } else {
            $this->favorites()->attach($micropostId);
            return true;
        }
    }
    
    public function unfavorite($micropostId)
    {
        $exist = $this->is_favorite($micropostId);
        $its_micropost = $this->id == $micropostId;
        
        if ($exist && !$its_micropost) {
            $this->favorites()->detach($micropostId);
            return true;
        } else {
            return false;
        }
    }
    
    public function is_favorite($micropostId) {
        return $this->favorites()->where('micropost_id', $micropostId)->exists();
    }
}
