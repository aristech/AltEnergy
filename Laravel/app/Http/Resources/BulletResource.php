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
            "mark_id" => $this->mark_id,
            "editable" => array([
                "supplement" => ["field" => "description", "title" => "ΠΕΡΙΓΡΑΦΗ", "type" => "text", "value" => $this->description, "required" => true]
            ])

        ];
    }
}
