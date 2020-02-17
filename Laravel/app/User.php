<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use App\Role;
use App\Damage;
use App\UsersRoles;
use App\Marks;
use App\Note;
use App\FreeAppointment;

class User extends Authenticatable
{
    use Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'lastname', 'firstname', 'active', 'role_id', 'telephone', 'telephone2', 'mobile', 'manager_id'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token', 'created_at', 'updated_at'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function role()
    {
        return $this->belongsToMany(Role::class);
    }

    public function notes()
    {
        return $this->hasMany(Note::class);
    }
    // public function damages()
    // {
    //     return $this->hasMany(Damage::class);
    // }
    public function free_appointments()
    {
        return $this->belongsToMany(FreeAppointment::class);
    }

    public function marks()
    {
        $this->belongsToMany(Mark::class);
    }
}
