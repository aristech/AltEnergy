<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Client;
use App\Device;
use App\User;
use App\DamageType;

class Damage extends Model
{
    protected $table = 'damages';

    protected $fillable = ['damage_type_id','damage_comments', 'cost', 'guarantee', 'status', 'appointment_pending', 'technician_left','technician_arrived','appointment_completed','appointment_needed','supplement_pending','damage_fixed', 'completed_no_transaction', 'client_id', 'manufacturer_id', 'mark_id', 'device_id' ,'user_id','supplement', 'comments',  'appointment_start', 'appointment_end'];

    protected $hidden = ['created_at', 'updated_at'];

    public function client()
    {
        return $this->belongsTo(Client::class, 'client_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class,'user_id');
    }

    public function device()
    {
        return $this->belongsTo(Device::class, 'device_id');
    }

    public function type()
    {
        return $this->belongsTo(DamageType::class,'damage_type_id');
    }


}
