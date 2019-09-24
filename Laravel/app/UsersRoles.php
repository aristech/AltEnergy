<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UsersRoles extends Model
{
   protected $table = 'role_user';

   protected $hidden = ['created_at','updated_at'];

   protected $fillable = ['user_id','role_id'];
}
