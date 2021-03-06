<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Manufacturer;
use App\Device;

class Mark extends Model
{
    protected $table = 'marks';

    protected $fillable = ['name','manufacturer_id'];

    protected $hidden = ['created_at','updated_at'];


    public function manufacturer()
    {
        return $this->belongsTo(Manufacturer::class,'manufacturer_id');
    }

    public function devices()
    {
        return $this->hasMany(Device::class,'device_id');
    }
}
