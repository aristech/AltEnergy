<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ScannerSettings extends Model
{
    protected $table = 'scanner_settings';

    protected $fillable = ['filename', 'title', 'disabled'];

    protected $hidden = ['created_at', 'updated_at'];
}
