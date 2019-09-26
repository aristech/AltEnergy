<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Mark;

class Manufacturer extends Model
{
    protected $table ='manufacturers';

    protected $fillable = ['name'];

    protected $hidden = ['created_at','updated_at'];

    public function marks()
    {
        return $this->hasMany(Mark::class);
    }
}
