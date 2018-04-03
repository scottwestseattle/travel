<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 
		'email', 
		'password', 
		'template_id', 
		'view_id', 
		'search_title_only_flag', 
		'search_whole_words_flag',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];
	
    public function tasks()
    {
    	return $this->hasMany(Task::class);
    }	
	
    public function entries()
    {
    	return $this->hasMany(Entry::class);
    }		
}
