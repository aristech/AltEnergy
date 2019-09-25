<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Client;

class Manager extends Model
{
    protected $table = 'managers';

    protected $fillable = ['lastname', 'firstname', 'telephone', 'telephone2', 'mobile', 'email'];

    protected $hidden = ['created_at','updated_at'];

    public function clients()
    {
        return $this->hasMany(Client::class);
    }

}
