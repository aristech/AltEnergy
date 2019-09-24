<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Manager extends Model
{
    protected $table = 'managers';

    protected $fillable = ['lastname', 'firstname', 'afm',	'doy', 'telephone',	'telephone2', 'mobile', 'address', 	'zipcode', 	'location_id'];

    protected $hidden = ['created_at','updated_at'];

    public function clients()
    {
        return $this->hasMany(App\Client::class);
    }

}
