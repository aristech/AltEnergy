<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\User;

class UsersRoles extends Model
{
   protected $table = 'role_user';

   protected $hidden = ['created_at','updated_at'];

   protected $fillable = ['user_id','role_id'];

   public function usrL()
   {
       return $this->belongsTo(App\User::class.'user_id');
   }
}
