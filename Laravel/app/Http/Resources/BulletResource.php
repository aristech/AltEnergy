<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BulletResource extends JsonResource
{
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
            "description" => $this->description,
            "price" =>$this->price,
            "mark_id" => $this->mark_id,
            "editable" => array([
                "description" => ["field" => "description", "title" => "ΠΕΡΙΓΡΑΦΗ", "type" => "text", "value" => $this->description, "required" => true],
                "price" => ["field" => "price", "title" => "Τιμη", "type" => "float", "value" => $this->price, "required" => true],

            ])

        ];
    }
}
