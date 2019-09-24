<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    protected $table = 'locations';

    protected $fillable = ['title'];

    protected $hidden = ['created_at','updated_at'];

    public function addresses()
    {
        return $this->hasMany(Address::class);
    }


}
