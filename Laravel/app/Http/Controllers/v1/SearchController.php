<?php

namespace App\Http\Controllers\v1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Client;
use App\Http\Resources\ClientResource;
use App\Manufacturer;
use App\Http\Resources\ManufacturerResource;
use App\Mark;
use App\Http\Resources\MarkResource;
use App\Device;
use App\Http\Resources\DeviceResource;
use App\Http\Resources\TechSearchResource;
use App\Manager;
use App\Http\Resources\ManagerResource;
use DB;
use App\DamageType;
use App\Http\Resources\DamageTypeResource;
use App\ServiceType;
use App\Http\Resources\ServiceTypeResource;

use App\Http\Resources\UserResource;

class SearchController extends Controller
{
    public function searchClients(Request $request)
    {
        $clients = Client::where('lastname', 'like', $request->name . '%')
            ->orWhere('lastname', 'like', '%' . $request->name . '%')
            ->orWhere('lastname', 'like', '%' . $request->name)
            ->orWhere('firstname', 'like', $request->name . '%')
            ->orWhere('firstname', 'like', '%' . $request->name . '%')
            ->orWhere('firstname', 'like', '%' . $request->name . '%')
            ->orWhere('firstname', 'like', '%' . $request->name)
            ->orderBy('lastname')
            ->get();

        if (!count($clients)) {
            return response()->json(["message" => "Δεν βρέθηκαν αποτελέσματα"], 404);
        }

        return ClientResource::collection($clients);
    }

    public function searchManagers(Request $request)
    {
        $managers = Manager::where('lastname', 'like', $request->name . '%')
            ->orWhere('lastname', 'like', '%' . $request->name . '%')
            ->orWhere('lastname', 'like', '%' . $request->name)
            ->orWhere('firstname', 'like', $request->name . '%')
            ->orWhere('firstname', 'like', '%' . $request->name . '%')
            ->orWhere('firstname', 'like', '%' . $request->name . '%')
            ->orWhere('firstname', 'like', '%' . $request->name)
            ->orderBy('lastname')
            ->get();

        if (!count($managers)) {
            return response()->json(["message" => "Δεν βρέθηκαν αποτελέσματα"], 404);
        }

        return ManagerResource::collection($managers);
    }

    public function searchTechs(Request $request)
    {
        $techs = DB::table('users')
            ->join('role_user', function ($join) {
                $join->on('users.id', '=', 'role_user.user_id')
                    ->where('role_user.role_id', '=', 3);
            })
            ->where('active', true)
            ->where('lastname', 'like', $request->name . '%')
            ->orWhere('lastname', 'like', '%' . $request->name . '%')
            ->orWhere('lastname', 'like', '%' . $request->name)
            ->orWhere('firstname', 'like', $request->name . '%')
            ->orWhere('firstname', 'like', '%' . $request->name . '%')
            ->orWhere('firstname', 'like', '%' . $request->name . '%')
            ->orWhere('firstname', 'like', '%' . $request->name)
            ->orderBy('lastname')
            ->get();

        if (!count($techs)) {
            return response()->json(["message" => "Δεν βρέθηκαν αποτελέσματα"], 404);
        }

        return TechSearchResource::collection($techs);
    }

    public function searchManufacturers(Request $request)
    {
        $manufacturers = Manufacturer::where('name', 'like', $request->name . '%')
            ->orWhere('name', 'like', '%' . $request->name . '%')
            ->orWhere('name', 'like', '%' . $request->name)
            ->orderBy('name')
            ->get();

        if (!count($manufacturers)) {
            return response()->json(["message" => "Δεν βρέθηκαν αποτελέσματα"], 404);
        }

        return ManufacturerResource::collection($manufacturers);
    }

    public function searchMarks(Request $request)
    {
        $marks = Mark::where('name', 'like', $request->name . '%')
            ->orWhere('name', 'like', '%' . $request->name . '%')
            ->orWhere('name', 'like', '%' . $request->name)
            ->orderBy('name')
            ->get();

        if (!count($marks)) {
            return response()->json(["message" => "Δεν βρέθηκαν αποτελέσματα"], 404);
        }

        return MarkResource::collection($marks);
    }

    public function searchDevices(Request $request)
    {
        $devices = Device::where('name', 'like', $request->name . '%')
            ->orWhere('name', 'like', '%' . $request->name . '%')
            ->orWhere('name', 'like', '%' . $request->name)
            ->orderBy('name')
            ->get();

        if (!count($devices)) {
            return response()->json(["message" => "Δεν βρέθηκαν αποτελέσματα"], 404);
        }

        return DeviceResource::collection($devices);
    }

    public function searchDamageTypes(Request $request)
    {
        $types = DamageType::where('name', 'like', $request->name . '%')
            ->orWhere('name', 'like', '%' . $request->name . '%')
            ->orWhere('name', 'like', '%' . $request->name)
            ->orderBy('name')
            ->get();

        if (!count($types)) {
            return response()->json(["message" => "Δεν βρέθηκαν αποτελέσματα"], 404);
        }

        return DamageTypeResource::collection($types);
    }

    public function searchServiceTypes(Request $request)
    {
        $types = ServiceType::where('name', 'like', $request->name . '%')
            ->orWhere('name', 'like', '%' . $request->name . '%')
            ->orWhere('name', 'like', '%' . $request->name)
            ->orderBy('name')
            ->get();

        if (!count($types)) {
            return response()->json(["message" => "Δεν βρέθηκαν αποτελέσματα"], 404);
        }

        return ServiceTypeResource::collection($types);
    }
}
