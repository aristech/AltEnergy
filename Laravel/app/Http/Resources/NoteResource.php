<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\User;

class NoteResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return
            [
                'resource' => 'notes',
                'id' => $this->id,
                'user' => $this->user['firstname'] . " " . $this->user['lastname'],
                'updated_by' => $this->when(true, function () {
                    $user = User::where('id', $this->updated_by)->first();
                    if (!$user) {
                        return null;
                    }
                    return $user['firstname'] . " " . $user['lastname'];
                }),
                'title' => $this->title,
                'description' => $this->description,
                'importance' => $this->importance,
                'all_day' => $this->all_day,
                'dateTime_start' => $this->dateTime_start,
                'dateTime_end'  => $this->dateTime_end,
                "editable" => array([
                    "resource" => "notes",
                    "id" => $this->id,
                    "info" => [
                        "client_lastname" => "NA",
                        "client_firstname" => "NA",
                        "client_address" => "NA",
                        "client_phone" =>  "NA"
                    ],
                    "title" =>  ["roles" => array(5, 4, 3), "field" => "title", "type" => "text", "title" => "Τίτλος Σημείωσης", "value" => $this->title, "required" => true],
                    "description" =>  ["roles" => array(5, 4, 3), "field" => "description", "type" => "text", "title" => "Σημείωση", "value" => $this->description, "required" => true],
                    "importance" => ["roles" => array(5, 4, 3), "field" => "importance", "value" => $this->importance, "type" => "boolean", "title" => "Σημαντικότητα", "radioItems" => [["id" => 0, "title" => "Υψηλή"], ["id" => 1, "title" => "Μεσαία"], ["id" => 2, "title" => "Χαμηλή"]], "required" => true],
                    "all_day" => ["roles" => array(5, 4, 3), "field" => "all_day", "value" => $this->all_day, "type" => "boolean", "title" => "Όλη μέρα", "radioItems" => [["id" => 1, "title" => "Ναι"], ["id" => 0, "title" => "Όχι"]], "required" => true],
                    "dateTime_start" => ["roles" => array(5, 4, 3), "field" => "dateTime_start", "title" => "Έναρξη", "type" => "datetime", "value" => $this->dateTime_start, "required" => true],
                    "dateTime_end" => ["roles" => array(5, 4, 3), "field" => "dateTime_end", "title" => "Λήξη", "type" => "datetime", "value" => $this->dateTime_end, "required" => true]
                ])
            ];
    }
}
