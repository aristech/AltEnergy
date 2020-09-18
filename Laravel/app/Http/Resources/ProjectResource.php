<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\User;
use App\Mark;
use App\DamageType;

class ProjectResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function fixedAddress()
    {
        $add = '';
        if ($this->client['address']) {
            $add .= $this->client['address'];
        }

        if ($this->client['location'] && $this->client['address']) {
            $add .= ', ' . $this->client['location'];
        } elseif ($this->client['location'] && !$this->client['address']) {
            $add .=  $this->client['location'];
        }

        if ($this->client['zipcode'] && ($this->client['address'] || $this->client['location'])) {
            $add .= ', ' . $this->client['zipcode'];
        } elseif ($this->client['zipcode'] && (!$this->client['address'] && !$this->client['location'])) {
            $add = $this->client['zipcode'];
        }

        return $add;
    }

    public function client_fullname()
    {
        if ($this->client['firstname'] && $this->client['lastname']) {
            $fullname = $this->client['firstname'] . " " . $this->client['lastname'];
        }

        if (!$this->client['firstname'] && !$this->client['lastname']) {
            $fullname = "";
        }

        if (!$this->client['firstname'] && $this->client['lastname']) {
            $fullname = $this->client['lastname'];
        }

        if ($this->client['firstname'] && !$this->client['lastname']) {
            $fullname = $this->client['firstname'];
        }

        return $fullname;
    }

    public function toArray($request)
    {
        return
            [
                'case_type' => 'ΕΡΓΟ',
                'case_name' => DamageType::where('id', $this->title_id)->first()['name'],
                'resource' => 'projects',
                "status" => $this->status,
                "id" => $this->id,
                "project_type" => DamageType::where('id', $this->title_id)->first()['name'],
                "comments" => $this->comments,
                "cost" => $this->cost,
                "appointment_pending_text" => "N/A",
                "manager_payment" => $this->manager_payment,
                "total_cost" => $this->cost + $this->manager_payment,
                "aitisi_eda" => $this->aitisi_eda,
                "aitisi_paroxou" => $this->aitisi_paroxou,
                "upografi_aitisis" => $this->upografi_aitisis,
                "parallagi_sxedion" => $this->parallagi_sxedion,
                "rantevou_xaraksis_metriti" => $this->rantevou_xaraksis_metriti,
                "topothetisi_metriti" => $this->topothetisi_metriti,
                "katathesi_meletis" => $this->katathesi_meletis,
                "egkrisi_meletis" => $this->egkrisi_meletis,
                "katathesi_pistopoihtikon" => $this->katathesi_pistopoihtikon,
                "udrauliki_egkatastasi" => $this->udrauliki_egkatastasi,
                "kleisimo_grammis_aeriou" => $this->kleisimo_grammis_aeriou,
                "dokimi_steganotitas" => $this->dokimi_steganotitas,
                "rantevou_elegxou" => $this->rantevou_elegxou,
                "rantevou_epanelegxou" => $this->rantevou_epanelegxou,
                "enausi" => $this->enausi,
                "ekdosi_fullou_kausis" => $this->ekdosi_fullou_kausis,
                "timologio" => $this->timologio,
                "client_id" => $this->client_id,
                "client_fullname" => $this->client_fullname(),
                "client_lastname" => $this->client['lastname'],
                "client_firstname" => $this->client['firstname'],
                "client_address" => $this->fixedAddress(),
                "client_phone" =>  $this->when(true, function () {
                    if ($this->client['telephone'] != null) return $this->client['telephone'];
                    if ($this->client['telephone2'] != null) return $this->client['telephone2'];
                    if ($this->client['mobile'] != null) return $this->client['mobile'];
                }),

                "devices" => $this->when(true, function () {
                    if ($this->marks === null) {
                        return "";
                    }
                    $detailed_device_array = array();
                    $devices_array = explode(',', $this->marks);
                    foreach ($devices_array as $d) {
                        $mark = Mark::where('id', $d)->first();
                        if ($mark) {
                            $device = $mark['manufacturer']['name'] . "/" . $mark['name'];
                            array_push($detailed_device_array, $device);
                        }
                    }

                    return implode(" , ", $detailed_device_array);
                }),
                "appointment_start" => $this->appointment_start,
                "appointment_end" => $this->appointment_end,
                "techs" => $this->when(true, function () {
                    $technicians = array();
                    if ($this->techs == null) {
                        return $technicians;
                    }
                    $techs = explode(',', $this->techs);
                    foreach ($techs as $tech) {
                        $technician = new \stdClass();
                        $techn = User::where('id', $tech)->where('active', true)->first();
                        if ($techn) {
                            $technician->tech_id = $tech;
                            $technician->tech_fullname = $techn['lastname'] . " " . $techn['firstname'];
                            array_push($technicians, $technician);
                        }
                    }
                    return $technicians;
                }),
                "marks" => $this->when(true, function () {
                    $markes = array();
                    if ($this->marks == null) {
                        return $markes;
                    }
                    $marks = explode(',', $this->marks);
                    foreach ($marks as $mark) {
                        $marka = new \stdClass();
                        $markk = Mark::where('id', $mark)->first();
                        if ($markk) {
                            $marka->id = $mark;
                            $marka->fullname = $markk['manufacturer']['name'] . ", " . $markk['name'];
                            array_push($markes, $marka);
                        }
                    }
                    return $markes;
                }),

                "editable" => array([
                    "resource" => "projects",
                    "id" => $this->id,
                    "info" => [
                        "client_lastname" => $this->client['lastname'],
                        "client_firstname" => $this->client['firstname'],
                        "client_address" => $this->client['address'] . "," . $this->client['location'] . "," . $this->client['zipcode'],
                        "client_phone" =>  $this->when(true, function () {
                            if ($this->client['telephone'] != null) return $this->client['telephone'];
                            if ($this->client['telephone2'] != null) return $this->client['telephone2'];
                            if ($this->client['mobile'] != null) return $this->client['mobile'];
                        })
                    ],
                    "project" => ["roles" => array(5, 4, 3), "field" => "title_id", "value" => $this->title_id, "type" => "search", "title" => "ΤΥΠΟΣ ΕΡΓΟΥ", "page" => "damagetypes", "holder" => DamageType::where('id', $this->title_id)->first()['name'], "required" => false],
                    "client" => ["roles" => array(5, 4, 3), "field" => "client_id", "value" => $this->client_id, "type" => "search", "title" => "ΠΕΛΑΤΗΣ", "page" => "clients", "holder" => $this->client['firstname'] . " " . $this->client['lastname'] . " | " . $this->client['address'], "required" => true],
                    "techs" => $this->when(true, function () {
                        $technicians = array();
                        $technician_ids = array();
                        if ($this->techs != null) {
                            $techs = explode(',', $this->techs);
                            foreach ($techs as $tech) {
                                $techn = User::where('id', $tech)->where('active', true)->first();
                                if ($techn) {
                                    array_push($technicians, $techn['lastname'] . " " . $techn['firstname']);
                                    $techno = new \stdClass();
                                    $techno->id = $techn['id'];
                                    $techno->fullname = $techn['firstname'] . " " . $techn['lastname'];
                                    $techno->email = $techn['email'];
                                    $techno->telephone = $techn['telephone'];
                                    $techno->telephone2 = $techn['telephone2'];
                                    $techno->mobile = $techn['mobile'];
                                    array_push($technician_ids, $techno);
                                }
                            }

                            $technicians = ["roles" => array(5, 4, 3), "title" => "ΤΕΧΝΙΚΟΙ", "field" => "techs", "type" => "searchtechs", "page" => "tech", "value" => $technician_ids, "holder" => $technicians, "required" => false];
                        } else {
                            $technicians = ["roles" => array(5, 4, 3), "title" => "ΤΕΧΝΙΚΟΙ", "field" => "techs", "type" => "searchtechs", "page" => "tech", "value" => array(), "holder" => array(), "required" => false];
                        }
                        return $technicians;
                    }),
                    "marks" => $this->when(true, function () {
                        $markes = array();
                        $markes_ids = array();
                        if ($this->marks != null) {
                            $marks = explode(',', $this->marks);
                            foreach ($marks as $mark) {
                                $marka = Mark::where('id', $mark)->first();
                                if ($marka) {
                                    array_push($markes, $marka['manufacturer']['name'] . ", " . $marka['name']);
                                    $markno = new \stdClass();
                                    $markno->id = $marka['id'];
                                    $markno->fullname = $marka['manufacturer']['name'] . ", " . $marka['name'];
                                    array_push($markes_ids, $markno);
                                }
                            }

                            $markes = ["roles" => array(5, 4, 3), "title" => "ΣΥΣΚΕΥΕΣ", "field" => "marks", "type" => "searchtechs", "page" => "searchmarks", "value" => $markes_ids, "holder" => $markes, "required" => false];
                        } else {
                            $markes = ["roles" => array(5, 4, 3), "title" => "ΣΥΣΚΕΥΕΣ", "field" => "marks", "type" => "searchtechs", "page" => "searchmarks", "value" => array(), "holder" => array(), "required" => false];
                        }
                        return $markes;
                    }),
                    "status" => ["roles" => array(5, 4, 3), "field" => "status", "value" => $this->status, "type" => "boolean", "title" => "ΚΑΤΑΣΤΑΣΗ", "radioItems" => [["id" => "Ολοκληρώθηκε", "title" => "Ολοκληρώθηκε"], ["id" => "Μη Ολοκληρωμένo", "title" => "Μη Ολοκληρωμένo"], ["id" => "Ακυρώθηκε", "title" => "Ακυρώθηκε"]], "required" => true],


                    "aitisi_eda" => ["roles" => array(5, 4, 3), "field" => "aitisi_eda", "value" => $this->aitisi_eda, "type" => "boolean", "title" => "ΑΙΤΗΣΗ ΕΔΑ", "radioItems" => [["id" => 0, "title" => "ΟΧΙ"], ["id" => 1, "title" => "ΝΑΙ"]], "required" => true],
                    "aitisi_paroxou" => ["roles" => array(5, 4, 3), "field" => "aitisi_paroxou", "value" => $this->aitisi_paroxou, "type" => "boolean", "title" => "ΑΙΤΗΣΗ ΠΑΡΟΧΟΥ", "radioItems" => [["id" => 0, "title" => "ΟΧΙ"], ["id" => 1, "title" => "ΝΑΙ"]], "required" => true],
                    "upografi_aitisis" => ["roles" => array(5, 4, 3), "field" => "upografi_aitisis", "value" => $this->upografi_aitisis, "type" => "boolean", "title" => "ΥΠΟΓΡΑΦΗ ΑΙΤΗΣΗΣ", "radioItems" => [["id" => 0, "title" => "ΟΧΙ"], ["id" => 1, "title" => "ΝΑΙ"]], "required" => true],
                    "parallagi_sxedion" => ["roles" => array(5, 4, 3), "field" => "parallagi_sxedion", "value" => $this->parallagi_sxedion, "type" => "boolean", "title" => "ΠΑΡΑΛΑΒΗ ΣΧΕΔΙΩΝ", "radioItems" => [["id" => 0, "title" => "ΟΧΙ"], ["id" => 1, "title" => "ΝΑΙ"]], "required" => true],
                    "rantevou_xaraksis_metriti" => ["roles" => array(5, 4, 3), "field" => "rantevou_xaraksis_metriti", "value" => $this->rantevou_xaraksis_metriti, "type" => "boolean", "title" => "ΡΑΝΤΕΒΟΥ ΧΑΡΑΞΗΣ ΜΕΤΡΗΤΗ", "radioItems" => [["id" => 0, "title" => "ΟΧΙ"], ["id" => 1, "title" => "ΝΑΙ"]], "required" => true],
                    "topothetisi_metriti" => ["roles" => array(5, 4, 3), "field" => "topothetisi_metriti", "value" => $this->topothetisi_metriti, "type" => "boolean", "title" => "ΤΟΠΟΘΕΤΗΣΗ ΜΕΤΡΗΤΗ", "radioItems" => [["id" => 0, "title" => "ΟΧΙ"], ["id" => 1, "title" => "ΝΑΙ"]], "required" => true],
                    "katathesi_meletis" => ["roles" => array(5, 4, 3), "field" => "katathesi_meletis", "value" => $this->katathesi_meletis, "type" => "boolean", "title" => "ΚΑΤΑΘΕΣΗ ΜΕΛΕΤΗΣ", "radioItems" => [["id" => 0, "title" => "ΟΧΙ"], ["id" => 1, "title" => "ΝΑΙ"]], "required" => true],
                    "egkrisi_meletis" => ["roles" => array(5, 4, 3), "field" => "egkrisi_meletis", "value" => $this->egkrisi_meletis, "type" => "boolean", "title" => "ΕΓΚΡΙΣΗ ΜΕΛΕΤΗΣ", "radioItems" => [["id" => 0, "title" => "ΟΧΙ"], ["id" => 1, "title" => "ΝΑΙ"]], "required" => true],
                    "katathesi_pistopoihtikon" => ["roles" => array(5, 4, 3), "field" => "katathesi_pistopoihtikon", "value" => $this->katathesi_pistopoihtikon, "type" => "boolean", "title" => "ΚΑΤΑΘΕΣΗ ΠΙΣΤΟΠΟΙΗΤΙΚΩΝ", "radioItems" => [["id" => 0, "title" => "ΟΧΙ"], ["id" => 1, "title" => "ΝΑΙ"]], "required" => true],
                    "udrauliki_egkatastasi" => ["roles" => array(5, 4, 3), "field" => "udrauliki_egkatastasi", "value" => $this->udrauliki_egkatastasi, "type" => "boolean", "title" => "ΥΔΡΑΥΛΙΚΗ ΕΓΚΑΤΑΣΤΑΣΗ", "radioItems" => [["id" => 0, "title" => "ΟΧΙ"], ["id" => 1, "title" => "ΝΑΙ"]], "required" => true],
                    "kleisimo_grammis_aeriou" => ["roles" => array(5, 4, 3), "field" => "kleisimo_grammis_aeriou", "value" => $this->kleisimo_grammis_aeriou, "type" => "boolean", "title" => "ΚΛΕΙΣΙΜΟ ΓΡΑΜΜΗΣ ΑΕΡΙΟΥ", "radioItems" => [["id" => 0, "title" => "ΟΧΙ"], ["id" => 1, "title" => "ΝΑΙ"]], "required" => true],
                    "dokimi_steganotitas" => ["roles" => array(5, 4, 3), "field" => "dokimi_steganotitas", "value" => $this->dokimi_steganotitas, "type" => "boolean", "title" => "ΔΟΚΙΜΗ ΣΤΕΓΑΝΟΤΗΤΑΣ", "radioItems" => [["id" => 0, "title" => "ΟΧΙ"], ["id" => 1, "title" => "ΝΑΙ"]], "required" => true],
                    "rantevou_elegxou" => ["roles" => array(5, 4, 3), "field" => "rantevou_elegxou", "value" => $this->rantevou_elegxou, "type" => "boolean", "title" => "ΡΑΝΤΕΒΟΥ ΕΛΕΓΧΟΥ", "radioItems" => [["id" => 0, "title" => "ΟΧΙ"], ["id" => 1, "title" => "ΝΑΙ"]], "required" => true],
                    "rantevou_epanelegxou" => ["roles" => array(5, 4, 3), "field" => "rantevou_epanelegxou", "value" => $this->rantevou_epanelegxou, "type" => "boolean", "title" => "ΡΑΝΤΕΒΟΥ ΕΠΑΝΕΛΕΓΧΟΥ", "radioItems" => [["id" => 0, "title" => "ΟΧΙ"], ["id" => 1, "title" => "ΝΑΙ"]], "required" => true],
                    "enausi" => ["roles" => array(5, 4, 3), "field" => "enausi", "value" => $this->enausi, "type" => "boolean", "title" => "ΕΝΑΥΣΗ", "radioItems" => [["id" => 0, "title" => "ΟΧΙ"], ["id" => 1, "title" => "ΝΑΙ"]], "required" => true],
                    "ekdosi_fullou_kausis" => ["roles" => array(5, 4, 3), "field" => "ekdosi_fullou_kausis", "value" => $this->ekdosi_fullou_kausis, "type" => "boolean", "title" => "ΕΚΔΟΣΗ ΦΥΛΛΟΥ ΚΑΥΣΗΣ", "radioItems" => [["id" => 0, "title" => "ΟΧΙ"], ["id" => 1, "title" => "ΝΑΙ"]], "required" => true],
                    "timologio" => ["roles" => array(5, 4, 3), "field" => "timologio", "value" => $this->timologio, "type" => "boolean", "title" => "ΤΙΜΟΛΟΓΙΟ", "radioItems" => [["id" => 0, "title" => "ΟΧΙ"], ["id" => 1, "title" => "ΝΑΙ"]], "required" => true],
                    "appointment_start" => ["roles" => array(5, 4, 3, 2), "field" => "appointment_start", "title" => "ΕΝΑΡΞΗ ΡΑΝΤΕΒΟΥ", "type" => "datetime", "value" => $this->appointment_start, "required" => false],
                    "appointment_end" => ["roles" => array(5, 4, 3), "field" => "appointment_end", "title" => "ΛΗΞΗ ΡΑΝΤΕΒΟΥ", "type" => "datetime", "value" => $this->appointment_end, "required" => false],
                    "cost" => ["roles" => array(5, 4, 3), "field" => "cost", "value" => $this->cost, "type" => "float", "title" => "ΤΙΜΗ", "required" => false],
                    "manager_payment" => ["roles" => array(5, 4, 3, 2), "field" => "manager_payment", "value" => $this->manager_payment, "type" => "float", "title" => "ΠΛΗΡΩΜΗ ΔΙΑΧΕΙΡΙΣΤΗ", "required" => false],
                    "total_cost" => ["roles" => array(5), "field" => "total_cost", "value" => $this->manager_payment + $this->cost, "type" => "float", "title" => "ΣΥΝΟΛΙΚΟ ΚΟΣΤΟΣ", "required" => false],
                    "comments" =>  ["roles" => array(5, 4, 3), "field" => "comments", "type" => "text", "title" => "ΓΕΝΙΚΑ ΣΧΟΛΙΑ", "value" => $this->comments, "required" => false]
                ])
            ];
    }
}
