<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Client;
use App\Manager;
use App\Location;

class Address extends Model
{
    protected $table = 'addresses';

    protected $fillable = ['address',' zipcode', 'level', 'location_id', 'client_id', 'manager_id'];

    protected $hidden = ['created_at','updated_at'];

    public function location()
    {
        return $this->belongsTo(Location::class,'location_id');
    }

    public function client()
    {
        return $this->belongsTo(Client::class,'client_id');
    }

    public function manager()
    {
        return $this->belongsTo(Manager::class,'manager_id');
    }
}
