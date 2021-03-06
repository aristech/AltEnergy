<?php

namespace App;

use App\User;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $table = 'roles';

    protected $fillable = ['title'];

    protected $hidden = ['created_at','updated_at'];

    public function users()
    {
        return $this->belongsToMany(User::class);
    }
}
