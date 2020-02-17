<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ClientMark extends Model
{
    protected $table = "client_mark";

    protected $fillable = ['mark_id', 'client_id'];
}
