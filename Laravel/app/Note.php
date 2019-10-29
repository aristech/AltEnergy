<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
Use App\User;

class Note extends Model
{
    protected $table = 'notes';

    protected $fillable = ['user_id', 'updated_by', 'title', 'importance', 'all_day', 'description', 'dateTime_start', 'dateTime_end'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
