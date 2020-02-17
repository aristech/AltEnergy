<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ClientResource extends JsonResource
{

    public function markNames($parameter)
    {
        $marks_array = array();

        foreach ($parameter as $mark) {
            array_push($marks_array, $mark['manufacturer']['name'] . ", " . $mark['name']);
        }

        return $marks_array;
    }

    public function markValues($parameter)
    {
        $marks_array = array();

        foreach ($parameter as $mark) {
            $markk = new \stdClass();
            $markk->id = $mark['id'];
            $markk->fullname = $mark['manufacturer']['name'] . ", " . $mark['name'];
            array_push($marks_array, $markk);
        }

        return $marks_array;
    }
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            "id" => $this->id,
            "lastname" => $this->lastname,
            "firstname" => $this->firstname,
            "afm" => $this->afm,
            "doy" => $this->doy,
            "arithmos_gnostopoihshs" => $this->arithmos_gnostopoihshs,
            "arithmos_meletis" => $this->arithmos_meletis,
            "arithmos_hkasp" => $this->arithmos_hkasp,
            "arithmos_aitisis" => $this->arithmos_aitisis,
            "plithos_diamerismaton" => $this->plithos_diamerismaton,
            "dieuthinsi_paroxis" => $this->dieuthinsi_paroxis,
            "kw_oikiako" => $this->kw_oikiako,
            "kw" => $this->kw,
            "marks" => $this->markValues($this->marks),
            "levitas" => $this->levitas,
            "telephone" => $this->telephone,
            "telephone2" => $this->telephone2,
            "mobile" => $this->mobile,
            "address" => $this->address,
            "zipcode" => $this->zipcode,
            "location" => $this->location,
            "email" => $this->email,
            "level" => $this->level,
            "manager_id" => $this->manager['id'],
            "manager_lastname" => $this->manager['lastname'],
            "manager_firstname" => $this->manager['firstname'],
            "manager_telephone" => $this->manager['telephone'],
            "manager_telephone2" => $this->manager['telephone2'],
            "manager_mobile" => $this->manager['mobile'],
            "manager_email" => $this->manager['email']
        ];
    }
}
