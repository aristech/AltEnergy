<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Service;

class ServiceType extends Model
{
    protected $table = 'service_types';

    protected $fillable = ['name'];

    protected $hidden = ["created_at", "updated_at"];

    public function services()
    {
        return $this->hasMany(Service::class,'service_type_id');
    }
}
