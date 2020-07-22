<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\UsersRoles;
use App\DamageType;
use App\Client;
use App\Mark;
use App\Project;
use App\Calendar;
use App\Http\Resources\ProjectResource;
use Validator;
use App\Service;

class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return ProjectResource::collection(Project::orderByRaw("CASE WHEN status = 'Μη Ολοκληρωμένο' THEN 1  WHEN status = 'Ολοκληρώθηκε' THEN 2 ELSE 3 END ASC")->get()); //Damage::where('status','Μη Ολοκληρωμένη')->get()
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
        // return $request;
        $validator = Validator::make($request->all(), ["cost" => "nullable|numeric", "manager_payment" => "nullable|numeric"]);

        if ($validator->fails()) {
            return response()->json(["message" => "Η τιμή και η αμοιβή διαχειριστή πρέπει να είναι αριθμοί"], 422);
        }

        $title = DamageType::where('id', $request->title_id)->first();
        if (!$title) {
            return response()->json(["message" => "Παρακαλώ εισάγετε έγκυρο τίτλο έργου"], 422);
        }

        $client = Client::where('id', $request->client_id)->first();
        if (!$client) {
            return response()->json(["message" => "Δεν βρέθηκε ο πελάτης"], 422);
        }

        $marks = $request->marks;
        if (count($marks) > 0) {
            foreach ($marks as $mark) {
                Mark::where('id', $mark)->first();
                if (!$mark) {
                    return response()->json(["message" => "Παρακαλώ ελέγξτε πάλι τις συσκεύες σας"]);
                }
            }
        }
        $techs = $request->techs;
        if ($techs) {
            foreach ($techs as $tech) {
                UsersRoles::where('user_id', $tech)->where('role_id', '3')->first();
            }
            if (!$tech) {
                return response()->json(["message" => "Παρακαλώ ελέγξτε πάλι τις συσκεύες σας"]);
            }
        }

        $techs = implode(',', $techs);

        $request->merge(['techs' => $techs]);
        $request->request->add(['status' => 'Μη Ολοκληρωμένo']);

        $marks = implode(',', $request->marks);
        $request->merge(['marks' => $marks]);

        if (!$request->cost) {
            $request->merge(['cost' => 0.00]);
        }

        if (!$request->manager_payment) {
            $request->merge(['manager_payment' => 0.00]);
        }



        $project = Project::create($request->all());
        if ($request->appointment_start != null) {
            Calendar::create(['name' => 'έργο', 'type' => 'projects', 'project_id' => $project->id]);
        }
        return response()->json(["message" => "Το νέο έργο αποθηκεύτηκε επιτυχώς"], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $projectId)
    {
        $project = Project::where('id', $projectId)->first();
        if (!$project) {
            return response()->json(["message" => "Το έργο δεν βρέθηκε"], 404);
        }
        return ProjectResource::make($project);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $projectId)
    {
        //return $request;
        $validator = Validator::make($request->all(), ["cost" => "nullable|numeric", "manager_payment" => "nullable|numeric"]);

        if ($validator->fails()) {
            return response()->json(["message" => "Η τιμή και η αμοιβή διαχειριστή πρέπει να είναι αριθμοί"], 422);
        }

        $title = DamageType::where('id', $request->title_id)->first();
        if (!$title) {
            return response()->json(["message" => "Παρακαλώ εισάγετε έγκυρο τίτλο έργου"], 422);
        }

        $client = Client::where('id', $request->client_id)->first();
        if (!$client) {
            return response()->json(["message" => "Δεν βρέθηκε ο πελάτης"], 422);
        }

        $marks_for_string = array();
        $marks = $request->marks;
        if (count($marks) > 0) {
            foreach ($marks as $mark) {
                Mark::where('id', $mark['id'])->first();
                if (!$mark) {
                    return response()->json(["message" => "Παρακαλώ ελέγξτε πάλι τις συσκεύες σας"], 404);
                }
                array_push($marks_for_string, $mark['id']);
            }
        }

        $techs_for_string = array();
        $techs = $request->techs;
        if ($techs) {
            foreach ($techs as $tech) {
                $myTech = UsersRoles::where('user_id', $tech['tech_id'])->where('role_id', '3')->first();
                array_push($techs_for_string, $tech['tech_id']);
            }
            if (!$myTech) {
                return response()->json(["message" => "Δεν βρέθηκε ο τεχνικός"], 404);
            }
        }


        $t = implode(',', $techs_for_string);

        $request->merge(['techs' => $t]);

        $m = implode(',',  $marks_for_string);
        $request->merge(['marks' => $m]);

        if (!$request->cost) {
            $request->merge(['cost' => 0.00]);
        }

        if (!$request->manager_payment) {
            $request->merge(['manager_payment' => 0.00]);
        }

        //
        if ($request->timologio == true || $request->status == "Ολοκληρώθηκε") {
            $request->merge(["timologio" => true]);
            $request->merge(["status" => "Ολοκληρώθηκε"]);
        }
        //
        $project = Project::where('id',  $projectId)->first();
        if (!$project) {
            return response()->json(["message" => "To συγκεκριμένο έργο δεν βρέθηκε"], 404);
        }
        $project->update($request->all());
        if ($request->appointment_start != null) {
            $calendar = Calendar::where('project_id', $request->id)->first();
            if (!$calendar) {
                Calendar::create(['name' => 'έργο', 'type' => 'projects', 'project_id' => $project->id]);
            }
        } else {
            Calendar::where('project_id', $request->id)->delete();
        }


        return response()->json(["message" => "Το έργο ενημερώθηκε επιτυχώς"], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        date_default_timezone_set('Europe/Athens');
        //return $request;
        $validator = Validator::make($request->all(), ["cost" => "nullable|numeric", "manager_payment" => "nullable|numeric"]);

        if ($validator->fails()) {
            return response()->json(["message" => "Η τιμή και η αμοιβή διαχειριστή πρέπει να είναι αριθμοί"], 422);
        }

        $title = DamageType::where('id', $request->title_id)->first();
        if (!$title) {
            return response()->json(["message" => "Παρακαλώ εισάγετε έγκυρο τίτλο έργου"], 422);
        }

        $client = Client::where('id', $request->client_id)->first();
        if (!$client) {
            return response()->json(["message" => "Δεν βρέθηκε ο πελάτης"], 422);
        }

        $marks_for_string = array();
        $marks = $request->marks;
        if (count($marks) > 0) {
            foreach ($marks as $mark) {
                if (is_int($mark)) {
                    array_push($marks_for_string, $mark);
                    $myMark = Mark::where('id', $mark)->first();
                } else {
                    array_push($marks_for_string, $mark['id']);
                    $myMark = Mark::where('id', $mark['id'])->first();
                }

                if (!$myMark) {
                    return response()->json(["message" => "Παρακαλώ ελέγξτε πάλι τις συσκεύες σας"], 404);
                }
            }
        }

        $techs_for_string = array();
        $techs = $request->techs;
        if ($techs) {
            foreach ($techs as $tech) {
                if (is_int($tech)) {
                    $myTech = UsersRoles::where('user_id', $tech)->where('role_id', '3')->first();
                    array_push($techs_for_string, $tech);
                } else {
                    $myTech = UsersRoles::where('user_id', $tech['id'])->where('role_id', '3')->first();
                    array_push($techs_for_string, $tech['id']);
                }

                if (!$myTech) {
                    return response()->json(["message" => "Δεν βρέθηκε ο τεχνικός"], 404);
                }
            }
        }


        $t = implode(',', $techs_for_string);

        $request->merge(['techs' => $t]);

        $m = implode(',', $marks_for_string);
        $request->merge(['marks' => $m]);

        if (!$request->cost) {
            $request->merge(['cost' => 0.00]);
        }

        if (!$request->manager_payment) {
            $request->merge(['manager_payment' => 0.00]);
        }

        $project = Project::where('id',  $request->id)->first();
        if (!$project) {
            return response()->json(["message" => "To συγκεκριμένο έργο δεν βρέθηκε"], 404);
        }
        //
        if ($request->timologio == true || $request->status == "Ολοκληρώθηκε") {
            $request->merge(["timologio" => true]);
            $request->merge(["status" => "Ολοκληρώθηκε"]);
        }
        //
        $project->update($request->all());
        if ($request->appointment_start != null) {
            $calendar = Calendar::where('project_id', $request->id)->first();
            if (!$calendar) {
                Calendar::create(['name' => 'έργο', 'type' => 'projects', 'project_id' => $project->id]);
            }
        } else {
            Calendar::where('project_id', $request->id)->delete();
        }

        //
        if ($request->timologio == true || $request->status == "Ολοκληρώθηκε") {
            foreach (explode(',', $request->marks) as $mark_id) {
                $mark = Mark::where('id', $mark_id)->first();
                if ($mark["guarantee_years"]) {
                    $timestamp_next = strtotime("+1 year");
                    $appointment_date = date('Y-m-d H:i:s', $timestamp_next);
                    $appointment_date = str_replace(" ", "T", $appointment_date);
                    $appointment_date = $appointment_date . ".000Z";

                    $guarantee_expiration = strtotime("+" . $mark["guarantee_years"] . " years");
                    $guarantee_expiration_date = date('Y-m-d H:i:s', $guarantee_expiration);
                    $guarantee_expiration_date = str_replace(" ", "T", $guarantee_expiration_date);
                    $guarantee_expiration_date = $guarantee_expiration_date . ".000Z";

                    $guarantee = true;
                } else {
                    $appointment_date = null;
                    $guarantee_expiration_date = null;
                    $guarantee = false;
                }
                Service::create([
                    "service_type_id2" => $request->title_id,
                    "status" => "Μη Ολοκληρωμένο",
                    "client_id" => $request->client_id,
                    "appointment_start" => $appointment_date,
                    "guarantee" => $guarantee,
                    "guarantee_end_date" =>  $guarantee_expiration_date,
                    "marks" => $mark_id
                ]);
            }
        }
        //

        return response()->json(["message" => "Το έργο ενημερώθηκε επιτυχώς"], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $project = Project::where('id', $request->id)->first();
        if (!$project) {
            return response()->json(["message" => "Το έργο με κωδικό " . $request->id . " δεν είναι καταχωρημένο!"], 404);
        }

        $project->delete();
        //delete stored entry in calendar
        $calendar = Calendar::where('project_id', $request->id)->first();
        if ($calendar) {
            $calendar->delete();
            //end delete calendar entry
        }



        return response()->json(["message" => "Το έργο με κωδικό " . $request->id . " διαγραφηκε επιτυχώς!"], 200);
    }

    public function remove(Request $request, $projectId)
    {
        $project = Project::where('id', $projectId)->first();
        if (!$project) {
            return response()->json(["message" => "Το έργο με κωδικό " . $projectId . " δεν είναι καταχωρημένη!"], 404);
        }

        $project->delete();
        //delete stored entry in calendar
        $calendar = Calendar::where('project_id', $projectId)->first();
        if ($calendar) {
            $calendar->delete();
            //end delete calendar entry
        }

        return response()->json(["message" => "Το έργο με κωδικό " . $projectId . " διαγραφηκε επιτυχώς!"], 200);
    }
}
