<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class EventResource extends JsonResource
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
            "id" => $this->id,
            "title" => $this->title,
            "status" => $this->status,
            "description" => $this->description,
            "comments" => $this->comments,
            "event_start" => $this->event_start,
            "event_end" => $this->event_end
        ];
    }
}
