<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\ServiceType;

class Service extends Model
{
    protected $table = 'services';

    protected $fillable = ['service_type_id2', 'service_comments', 'cost', 'guarantee', 'status', 'appointment_pending', 'technician_left', 'technician_arrived', 'appointment_completed', 'appointment_needed', 'supplement_pending', 'service_completed', 'completed_no_transaction', 'client_id', 'manufacturer_id', 'mark_id', 'device_id', 'supplements', 'comments', 'user_id', 'techs', 'appointment_start', 'appointment_end', 'repeatable', 'frequency'];

    protected $hidden = ['created_at', 'updated_at'];

    public function client()
    {
        return $this->belongsTo(Client::class, 'client_id');
    }

    // public function user()
    // {
    //     return $this->belongsTo(User::class,'user_id');
    // }

    public function device()
    {
        return $this->belongsTo(Device::class, 'device_id');
    }

    public function type()
    {
        return $this->belongsTo(DamageType::class, 'service_type_id2');
    }
}
