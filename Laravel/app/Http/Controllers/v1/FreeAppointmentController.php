<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\FreeAppointmentResource;
use App\FreeAppointment;
use App\FreeAppointmentUser;
use App\Http\CustomClasses\v1\AuthorityClass;
use App\Http\CustomClasses\v1\FreeAppointmentClass;
use Validator;
use App\User;
use App\Calendar;
use App\Http\Resources\CalendarResource;

class FreeAppointmentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $highest_role = AuthorityClass::getAuthorityLevel($request);
        if ($highest_role < 3) {
            return;
        } elseif ($highest_role == 3) {
            return FreeAppointmentResource::collection($request->user()->free_appointments);
        } else {
            return FreeAppointmentResource::collection(FreeAppointment::all());
        }
    }

    public function indexTwo(Request $request)
    {
        $highest_role = AuthorityClass::getAuthorityLevel($request);
        if ($highest_role < 3) {
            return;
        } elseif ($highest_role == 3) {
            $appointments = FreeAppointmentResource::collection($request->user()->free_appointments()->where('appointment_start', '!=', null)->get());
        } else {
            $appointments = FreeAppointmentResource::collection(FreeAppointment::where("appointment_start", "!=", null)->get());
        }

        $calendar = CalendarResource::collection(Calendar::where('note_id', '!=', null)->get());
        $result = $appointments->merge($calendar);
        return $result;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $highest_role = AuthorityClass::getAuthorityLevel($request);
        if ($highest_role < 3) {
            return response()->json(["message" => "Δεν μπορείτε να έχετε πρόσβαση"], 422);
        }
        //$validator = Validator::make($input, $rules, $messages);
        $validator = Validator::make($request->all(), FreeAppointmentClass::$rules, FreeAppointmentClass::$messages);
        if ($validator->fails()) {
            return response()->json(["message" => $validator->errors()->first()], 422);
        }

        if (count($request->techs) > 0) {
            foreach ($request->techs as $technician) {
                $t = User::where('id', $technician)->whereHas('role', function ($q) {
                    $q->where('title', 'Τεχνικός');
                })->where('active', true)->first();

                if (!$t) {
                    return response()->json(["message" => "Παρακαλώ ελέγξτε πάλι τους τεχνικούς που εισάγατε"], 422);
                }
            }
        }

        $appointment = FreeAppointment::create($request->all());
        if (count($request->techs) > 0) {
            foreach ($request->techs as $technician) {
                FreeAppointmentUser::create(['user_id' => $technician, 'free_appointment_id' => $appointment->id]);
            }
        }

        return response()->json(["message" => "Το ελεύθερο ραντεβού δημιουργήθηκε επιτυχώς"], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $appointment)
    {
        $free_appointment = FreeAppointment::find($appointment);
        if (!$free_appointment) {
            return response()->json(["message" => "Δεν βρέθηκε το ραντεβού καταχωρημένο στο σύστημα"], 404);
        }

        $highest_role = AuthorityClass::getAuthorityLevel($request);
        $verified_technician = FreeAppointmentUser::where('free_appointment_id', $appointment)->where('user_id', $request->user()->id)->first();
        if ($highest_role < 4 && !$verified_technician) {
            return response()->json(["message" => "Δεν μπορείτε να έχετε πρόσβαση"], 422);
        }

        return  FreeAppointmentResource::make($free_appointment);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request)
    {
        $free_appointment = FreeAppointment::find($request->id);
        //$free_appointment = FreeAppointment::where('id', $appointment)->first();
        if (!$free_appointment) {
            return response()->json(["message" => "Δεν βρέθηκε το ραντεβού καταχωρημένο στο σύστημα"], 404);
        }

        $highest_role = AuthorityClass::getAuthorityLevel($request);
        $verified_technician = FreeAppointmentUser::where('free_appointment_id', $request->id)->where('user_id', $request->user()->id)->first();
        if ($highest_role < 4 && !$verified_technician) {
            return response()->json(["message" => "Δεν μπορείτε να έχετε πρόσβαση"], 422);
        }


        $validator = Validator::make($request->all(), FreeAppointmentClass::$rules, FreeAppointmentClass::$messages);
        if ($validator->fails()) {
            return response()->json(["message" => $validator->errors()->first()], 422);
        }

        if (count($request->techs) > 0) {
            foreach ($request->techs as $technician) {
                $t = User::where('id', $technician)->whereHas('role', function ($q) {
                    $q->where('title', 'Τεχνικός');
                })->where('active', true)->first();

                if (!$t) {
                    return response()->json(["message" => "Παρακαλώ ελέγξτε πάλι τους τεχνικούς που εισάγατε"], 422);
                }
            }
        }

        $free_appointment->update($request->all());
        FreeAppointmentUser::where('free_appointment_id', $free_appointment->id)->delete();
        if (count($request->techs) > 0) {
            foreach ($request->techs as $technician) {
                FreeAppointmentUser::create(['user_id' => $technician, 'free_appointment_id' => $free_appointment->id]);
            }
        }

        return response()->json(["message" => "Το ελεύθερο ραντεβού ενημερώθηκε επιτυχώς"], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $appointment)
    {
        $free_appointment = FreeAppointment::find($appointment);
        //$free_appointment = FreeAppointment::where('id', $appointment)->first();
        if (!$free_appointment) {
            return response()->json(["message" => "Δεν βρέθηκε το ραντεβού καταχωρημένο στο σύστημα"], 404);
        }

        $highest_role = AuthorityClass::getAuthorityLevel($request);
        $verified_technician = FreeAppointmentUser::where('free_appointment_id', $appointment)->where('user_id', $request->user()->id)->first();
        if ($highest_role < 4 && !$verified_technician) {
            return response()->json(["message" => "Δεν μπορείτε να έχετε πρόσβαση"], 422);
        }


        $validator = Validator::make($request->all(), FreeAppointmentClass::$rules, FreeAppointmentClass::$messages);
        if ($validator->fails()) {
            return response()->json(["message" => $validator->errors()->first()], 422);
        }

        if (count($request->techs) > 0) {
            foreach ($request->techs as $technician) {
                $t = User::where('id', $technician)->whereHas('role', function ($q) {
                    $q->where('title', 'Τεχνικός');
                })->where('active', true)->first();

                if (!$t) {
                    return response()->json(["message" => "Παρακαλώ ελέγξτε πάλι τους τεχνικούς που εισάγατε"], 422);
                }
            }
        }

        $free_appointment->update($request->all());
        FreeAppointmentUser::where('free_appointment_id', $free_appointment->id)->delete();
        if (count($request->techs) > 0) {
            foreach ($request->techs as $technician) {
                FreeAppointmentUser::create(['user_id' => $technician, 'free_appointment_id' => $free_appointment->id]);
            }
        }

        return response()->json(["message" => "Το ελεύθερο ραντεβού ενημερώθηκε επιτυχώς"], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $appointment)
    {
        $free_appointment = FreeAppointment::find($appointment);
        if (!$free_appointment) {
            return response()->json(["message" => "Δεν βρέθηκε το ραντεβού καταχωρημένο στο σύστημα"], 404);
        }

        $highest_role = AuthorityClass::getAuthorityLevel($request);
        $verified_technician = FreeAppointmentUser::where('free_appointment_id', $appointment)->where('user_id', $request->user()->id)->first();
        if ($highest_role < 4 && !$verified_technician) {
            return response()->json(["message" => "Δεν μπορείτε να έχετε πρόσβαση"], 422);
        }

        $free_appointment->delete();
        FreeAppointmentUser::where('free_appointment_id', $free_appointment->id)->delete();

        return response()->json(["message" => "Το ελεύθερο ραντεβού διαγράφηκε επιτυχώς"], 200);
    }
}
