<?php

namespace App\Http\CustomClasses\v1;
use App\Damage;
use Validator;
use App\Device;

class DamageSuperAdmin
{
    protected $request;
    protected $hasError = false;
    protected $error;
    protected $message;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function getDamages()
    {
        $damages = Damage::all();
        return $damages;
    }

    protected function validatorCreate()
    {
        $validator = Validator::make($this->request->all(),
        [
            'damage_type' => 'required|string',
            'damage_comments' => 'nullable|min:4|max:10000',
            'cost' => 'nullable|numeric|between:0.00,999999.99',
            'guarantee' => 'nullable|boolean',
            'status' => 'required|string',
            'client_id' => 'required|integer',
            'device_id' => 'required|integer',
            'comments' => 'nullable|min:4|max:100000',
            'manufacturer_id' => 'required|integer',
            'mark_id' => 'required|integer',
            'device_id' => 'required|integer'
        ]);

        if($validator->fails())
        {
            $this->hasError = true;
            $this->error = response()->json(["message" => $validator->errors()->first()],422);
        }

        $manufacturer_id = $this->request->manufacturer_id;
        $mark_id = $this->request->mark_id;
        $device_id = $this->request->device_id;

        $device = Device::whereHas('mark', function($query) use($mark_id)
        {
            $query->where('id',$mark_id);

        })
        ->whereHas('mark.manufacturer', function($query) use ($manufacturer_id)
        {
            $query->where('id',$manufacturer_id);
        })
        ->where('id',$device_id)->first();

        if(!$device)
        {
            $this->hasErrors = true;
            $this->error = response()->json(["message"=>"Η συσκευή που εισάγατε δεν υπάρχει στο σύστημα. Βεβαιωθείτε ότι τα στοιχεία της συσκευης είναι σωστά!"],422);
        }

        $client = Client::where('id',$this->request->device_id)->first();
        if(!$client)
    }

    public function storeDamage()
    {
        if($this->hasErrors == true)
        {
            return $this->error;
        }

        Damage::create($request->all());

        return response()->json(["message" => "Η ζημιά του πελάτη"])
    }


}










}
