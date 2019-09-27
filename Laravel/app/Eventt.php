<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Eventt extends Model
{
    protected $table = 'events';

    protected $fillable = ['event_type', 'event_id', 'event_title', 'repeatable', 'start_time', 'end_time'];

    protected $hidden = ['created_at', 'updated_at'];
}
