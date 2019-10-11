<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Eventt extends Model
{
    protected $table = 'event';

    protected $fillable = ['title',	'status', 'description', 'comments',  'event_start','event_end'];

}
