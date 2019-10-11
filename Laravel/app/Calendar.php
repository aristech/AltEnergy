<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Calendar extends Model
{
    protected $table = "calendar";

    protected $fillable = ["type", 'damage_id', 'service_id','offer_id', 'event_id'];


}
