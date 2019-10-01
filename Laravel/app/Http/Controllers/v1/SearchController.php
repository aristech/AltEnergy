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
use App\Http\Resources\Device;
use App\Http\Resources\DeviceResource;
use App\Http\Resources\TechSearchResource;
use DB;

use App\Http\Resources\UserResource;

class SearchController extends Controller
{
   public function searchClients(Request $request)
   {
        $clients = Client::where('lastname','like',$request->name.'%')
        ->orWhere('lastname','like','%'.$request->name.'%')
        ->orWhere('lastname','like','%'.$request->name)
        ->orWhere('firstname','like',$request->name.'%')
        ->orWhere('firstname','like','%'.$request->name.'%')
        ->orWhere('firstname','like','%'.$request->name.'%')
        ->orWhere('firstname','like','%'.$request->name)
        ->orderBy('lastname')
        ->get();

        if(!count($clients))
        {
            return response()->json(["message" => "Δεν βρέθηκαν αποτελέσματα"],404);
        }

        return ClientResource::collection($clients);
   }

   public function searchTechs(Request $request)
   {
        $techs = DB::table('users')
        ->join('role_user', function ($join)
        {
            $join->on('users.id', '=', 'role_user.user_id')
                ->where('role_user.role_id', '=', 3);
        })
        ->where('lastname','like',$request->name.'%')
        ->orWhere('lastname','like','%'.$request->name.'%')
        ->orWhere('lastname','like','%'.$request->name)
        ->orWhere('firstname','like',$request->name.'%')
        ->orWhere('firstname','like','%'.$request->name.'%')
        ->orWhere('firstname','like','%'.$request->name.'%')
        ->orWhere('firstname','like','%'.$request->name)
        ->orderBy('lastname')
        ->get();

        if(!count($techs))
        {
            return response()->json(["message" => "Δεν βρέθηκαν αποτελέσματα"],404);
        }

        return TechSearchResource::collection($techs);
   }

   public function searchManufacturer(Request $request)
   {
        $manufacturers = Manufacturer::where('name','like',$request->name.'%')
        ->orWhere('name','like','%'.$request->name.'%')
        ->orWhere('name','like','%'.$request->name)
        ->orderBy('name')
        ->get();

        if(!count($manufacturers))
        {
            return response()->json(["message" => "Δεν βρέθηκαν αποτελέσματα"],404);
        }

        return ManufacturerResource::collection($manufacturers);
   }

   public function searchMark(Request $request)
   {
        $marks = Mark::where('name','like',$request->name.'%')
        ->orWhere('name','like','%'.$request->name.'%')
        ->orWhere('name','like','%'.$request->name)
        ->orderBy('name')
        ->get();

        if(!count($marks))
        {
            return response()->json(["message" => "Δεν βρέθηκαν αποτελέσματα"],404);
        }

        return MarkResource::collection($marks);
   }

   public function searchDevice(Request $request)
   {
        $devices = Device::where('name','like',$request->name.'%')
        ->orWhere('name','like','%'.$request->name.'%')
        ->orWhere('name','like','%'.$request->name)
        ->orderBy('name')
        ->get();

        if(!count($devices))
        {
            return response()->json(["message" => "Δεν βρέθηκαν αποτελέσματα"],404);
        }

        return DeviceResource::collection($devices);
   }
}
