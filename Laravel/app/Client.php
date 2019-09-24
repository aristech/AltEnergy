<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Address;

class Client extends Model
{
    protected $table = 'clients';

    protected $fillable = ['lastname', 'firstname', 'afm', 'doy', 'telephone', 'telephone2', 'mobile'];

    protected $hidden = ['created_at','updated_at'];

    public function addresses()
    {
        return $this->hasMany(Address::class);
    }
}
