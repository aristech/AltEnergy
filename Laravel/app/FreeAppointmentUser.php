<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FreeAppointmentUser extends Model
{
    protected $table = "free_appointment_user";

    protected $fillable = ['user_id', "free_appointment_id"];
}
