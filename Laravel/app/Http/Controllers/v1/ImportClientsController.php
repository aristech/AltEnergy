<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Client;
use App\Http\CustomClasses\v1\Greeklish;

class ImportClientsController extends Controller
{
    public function import(Request $request)
    {
        $role_id = $request->user()->role()->first()->id;
        if ($role_id < 4) {
            return response()->json(["message" => "Δεν έχετε δικαίωμα να εκτελέσετε την συγκεκριμένη ενέργεια!"], 401);
        }

        move_uploaded_file($request->file, storage_path() . '/test20.csv');
        $rows  = array_map('str_getcsv', file(storage_path() . '/test20.csv'));

        foreach ($rows as $row) {

            $input = array();

            $value = "";
            if ($row[0] == "") {
                $value = "N/A";
            } else {
                $value = $row[0];
            }
            $input['lastname'] = $value;

            $value = "";
            if ($row[1] == "") {
                $value = "N/A";
            } else {
                $value = $row[1];
            }
            $input['firstname'] = $value;

            $value = "";
            if ($row[2] == "") {
                $value = "N/A";
            } else {
                $value = $row[2];
            }
            $input['address'] = $value;

            $value = "";
            if ($row[3] == "") {
                $value = "N/A";
            } else {
                $value = $row[3];
            }
            $input['location'] = $value;

            if ($row[4] == "" && $row[5] == "" && $row[6] == "") {
                $input["telephone1"] = "N/A";
                $input["telephone2"] = "";
                $input["mobile"] = "";
            } else {
                $input["telephone1"] = $row[4];
                $input["telephone2"] = $row[5];
                $input["mobile"] = $row[6];
            }

            $input["zipcode"] = "N/A";
            $input["floor"] = "N/A";
            $input["email"] = $row[7];

            $lastClient = Client::latest()->first();
            $current_id = $lastClient['id'] + 1;

            //$request->request->add(["foldername" => $foldername]);
            $input['foldername'] = "--";

            $client = Client::create($input);
            $foldername = str_replace(array("/", " "), "", $input['lastname']) . "_" . str_replace(array("/", " "), "", $input['firstname']);
            $foldername = strtolower(Greeklish::remove_accent($foldername) . '_' . $client->id); //conversion to greeklish
            $client->update(["foldername" => $foldername]);

            if (!$folder_created = mkdir(storage_path() . "/Clients/$foldername")) {
                return response()->json(["message" => "Δεν μπόρεσε να δημιουργηθεί φάκελος για τον πελάτη " . $input['lastname'] . " " . $input['firstname']], 500);
            }
        }

        unlink(storage_path() . '/test20.csv');
        return response()->json(["message" => "Τα στοιχεία περάστικαν επιτυχως!"], 200);
    }
}
