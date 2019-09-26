<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Client;
use App\Device;

class Damage extends Model
{
    protected $table = 'damages';

    protected $fillable = ['damage_type','damage_comments', 'cost', 'guarantee', 	'status', 	'estimation_appointment', 	'cost_information', 	'supplement_available' 	,'fixing_appointment', 	'damage_fixed', 	'damage_paid', 	'client_id', 	'device_id' ,'supplement', 	'comments'];

    protected $hidden = ['updated_at'];

    public function client()
    {
        return $this->belongsTo(Client::class, 'client_id');
    }

    public function device()
    {
        return $this->belongsTo(Device::class, 'device_id');
    }
}
