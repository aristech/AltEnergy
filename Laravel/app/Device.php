<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Mark;

class Device extends Model
{
    protected $table = 'devices';

    protected $fillable = ['name','mark_id'];

    protected $hidden = ['created_at','updated_at'];


    public function marks()
    {
        return $this->belongsTο(Mark::class);
    }

    public function damages()
    {
        return $this->hasMany(Damage::class);
    }
}
