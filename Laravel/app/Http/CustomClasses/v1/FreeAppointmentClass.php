<?php

namespace App\Http\CustomClasses\v1;

class FreeAppointmentClass
{
    public static $rules = ['appointment_title' => 'nullable|string', 'appointment_description' => 'nullable|string', 'appointment_completed' => 'required|boolean', 'appointment_start' => 'required|string', 'appointment_end' => 'nullable|string'];

    public static $messages = ['required' => 'Η ημ/νια έναρξης και κατάσταση ραντεβού είναι υποχρεωτικά πεδία'];
}
