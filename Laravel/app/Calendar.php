<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Calendar extends Model
{
    protected $table = "calendar";

    protected $fillable = ["name", "type", 'damage_id', 'service_id', 'offer_id', 'event_id', 'note_id', 'project_id'];
}
