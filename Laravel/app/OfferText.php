<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Offer;

class OfferText extends Model
{
    protected $table = "offer_texts";

    protected $fillable = ["type", "upper_text", "lower_text"];

    public function offers()
    {
        return $this->hasMany(Offer::class);
    }
}
