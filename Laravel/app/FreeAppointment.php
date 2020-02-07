<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\User;

class FreeAppointment extends Model
{
    protected $table = 'free_appointments';

    protected $fillable = ['appointment_title', 'appointment_description', 'appointment_completed', 'appointment_location', 'appointment_start', 'appointment_end'];

    public function users()
    {
        return $this->belongsToMany(User::class);
    }
}
